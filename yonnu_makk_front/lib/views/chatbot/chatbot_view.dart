import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:record/record.dart';
import 'package:audioplayers/audioplayers.dart';
import 'package:path_provider/path_provider.dart';
import 'package:flutter_tts/flutter_tts.dart';
import '../../config/theme.dart';
import '../../services/api_service.dart';
import '../../widgets/bottom_nav.dart';

// ─── VUE CHATBOT IA (texte + voix, français / wolof) ───────────────────────────
class ChatbotView extends StatefulWidget {
  const ChatbotView({super.key});
  @override
  State<ChatbotView> createState() => _ChatbotViewState();
}

class _ChatbotViewState extends State<ChatbotView> {
  final TextEditingController _ctrl     = TextEditingController();
  final ScrollController      _scroll   = ScrollController();
  final AudioRecorder         _recorder = AudioRecorder();
  final AudioPlayer           _player   = AudioPlayer();
  final FlutterTts            _tts      = FlutterTts();

  bool    _loading   = false;
  bool    _recording = false;
  bool    _voiceOn   = true;      // lecture vocale automatique des réponses
  String  _langue    = 'fr';      // 'fr' ou 'wo'
  String? _sessionId;

  static const _msgAccueilFr =
      'Bonjour ! Je suis votre assistant santé YOONU JIGEEN 🌸\n'
      'Posez vos questions sur la grossesse et le suivi prénatal, '
      'par écrit ou avec le micro 🎤. Je peux aussi vous répondre à voix haute.';

  static const _msgAccueilWo =
      'Bonjour ! Man mooy assistant santé YOONU JIGEEN 🌸\n'
      'Laajal sama ay laaj ci ëmbë ak suivi prénatal, '
      'ci bindëfu wala ak mikro 🎤. Mën naa tamit la tontu ak xam-xam.';

  final List<_Msg> _messages = [
    _Msg(text: _msgAccueilFr, isUser: false),
  ];

  @override
  void initState() {
    super.initState();
    _initTts();
    _chargerHistorique();
  }

  Future<void> _initTts() async {
    // Privilégier le moteur Google (voix neuronales de meilleure qualité) sur Android.
    try {
      final moteurs = (await _tts.getEngines) as List?;
      if (moteurs != null && moteurs.contains('com.google.android.tts')) {
        await _tts.setEngine('com.google.android.tts');
      }
    } catch (_) {}

    await _tts.setLanguage('fr-FR');
    await _choisirMeilleureVoixFr();

    await _tts.setSpeechRate(0.46);  // débit posé, plus naturel
    await _tts.setPitch(1.05);       // timbre légèrement plus chaleureux
    await _tts.setVolume(1.0);
    await _tts.awaitSpeakCompletion(true);
  }

  /// Sélectionne la voix française la plus naturelle parmi celles installées
  /// (préférence aux voix réseau/améliorées, puis à une voix féminine).
  Future<void> _choisirMeilleureVoixFr() async {
    try {
      final voix = (await _tts.getVoices) as List?;
      if (voix == null) return;

      final fr = voix
          .whereType<Map>()
          .where((v) => (v['locale'] ?? '').toString().toLowerCase().startsWith('fr'))
          .toList();
      if (fr.isEmpty) return;

      int score(Map v) {
        final n = (v['name'] ?? '').toString().toLowerCase();
        var s = 0;
        if (n.contains('network')) s += 4;       // voix réseau = plus naturelle
        if (n.contains('enhanced') || n.contains('premium')) s += 3;
        if (RegExp(r'(fr-fr|fre)').hasMatch((v['locale'] ?? '').toString().toLowerCase())) s += 2;
        if (n.contains('female') || n.contains('-f-') || n.endsWith('f')) s += 1;
        return s;
      }

      fr.sort((a, b) => score(b).compareTo(score(a)));
      final meilleure = fr.first;
      await _tts.setVoice({
        'name':   meilleure['name'].toString(),
        'locale': meilleure['locale'].toString(),
      });
    } catch (_) {/* on garde la voix par défaut si indisponible */}
  }

  @override
  void dispose() {
    _ctrl.dispose();
    _scroll.dispose();
    _recorder.dispose();
    _player.dispose();
    _tts.stop();
    super.dispose();
  }

  /// Lit un message à voix haute.
  /// - En mode wolof : joue l'audio pré-enregistré uniquement (pas de TTS synthétique).
  /// - En mode français : audio serveur si présent, sinon TTS local.
  Future<void> _parler(_Msg m) async {
    await _tts.stop();
    if (m.audioUrl != null) {
      try { await _player.play(UrlSource(m.audioUrl!)); return; } catch (_) {}
    }
    // En wolof, on ne tombe pas en fallback TTS (qualité trop mauvaise).
    if (_langue == 'wo') return;
    final texte = m.text.trim();
    if (texte.isNotEmpty) {
      try { await _tts.speak(texte); } catch (_) {}
    }
  }

  /// Bascule la langue FR ↔ WO et remet un message d'accueil adapté.
  void _toggleLangue() {
    _tts.stop();
    setState(() {
      _langue = _langue == 'fr' ? 'wo' : 'fr';
      // Réinitialise la conversation avec le bon message d'accueil.
      _messages
        ..clear()
        ..add(_Msg(
            text: _langue == 'wo' ? _msgAccueilWo : _msgAccueilFr,
            isUser: false));
      _sessionId = null;
    });
  }

  // ── Historique ──────────────────────────────────────────────────────────────
  Future<void> _chargerHistorique() async {
    final res = await ApiService.instance.get('/patient/chatbot/historique');
    if (!mounted || !res.ok) return;
    final data = (res.data['data'] as List? ?? []);
    if (data.isEmpty) return;
    // L'API renvoie du plus récent au plus ancien → on remet dans l'ordre.
    final items = data.reversed
        .map((e) => _Msg(
              text:   e['message'] as String? ?? '',
              isUser: e['role'] == 'utilisateur',
            ))
        .toList();
    setState(() {
      _messages
        ..clear()
        ..addAll(items);
      _sessionId = data.first['session_id'] as String?;
    });
    _scrollToBottom();
  }

  // ── Envoi texte ───────────────────────────────────────────────────────────
  Future<void> _send([String? preset]) async {
    final text = (preset ?? _ctrl.text).trim();
    if (text.isEmpty || _loading) return;
    setState(() {
      _messages.add(_Msg(text: text, isUser: true));
      _loading = true;
      _ctrl.clear();
    });
    _scrollToBottom();

    final res = await ApiService.instance.post('/patient/chatbot', body: {
      'message':    text,
      'langue':     _langue,
      'session_id': ?_sessionId,
    });
    if (!mounted) return;

    if (res.ok) {
      _sessionId = res.data['session_id'] as String? ?? _sessionId;
      final reponse = _Msg(
        text:     res.data['message']?['message'] as String? ?? '…',
        isUser:   false,
        audioUrl: res.data['audio_url'] as String?,
      );
      setState(() {
        _messages.add(reponse);
        _loading = false;
      });
      if (_voiceOn) _parler(reponse);
    } else {
      setState(() {
        _messages.add(_Msg(
          text: res.error ?? 'Désolée, une erreur est survenue. Réessayez.',
          isUser: false));
        _loading = false;
      });
    }
    _scrollToBottom();
  }

  // ── Enregistrement vocal ────────────────────────────────────────────────────
  Future<void> _toggleRecord() async {
    if (kIsWeb) {
      _snack('La saisie vocale est disponible sur l\'application mobile.');
      return;
    }
    if (_recording) {
      final path = await _recorder.stop();
      setState(() => _recording = false);
      if (path != null) await _envoyerAudio(path);
      return;
    }
    if (await _recorder.hasPermission()) {
      final dir  = await getTemporaryDirectory();
      final path = '${dir.path}/voix_${DateTime.now().millisecondsSinceEpoch}.m4a';
      await _recorder.start(const RecordConfig(), path: path);
      setState(() => _recording = true);
    } else {
      _snack('Accès au micro refusé.');
    }
  }

  Future<void> _envoyerAudio(String path) async {
    setState(() => _loading = true);
    _scrollToBottom();
    final res = await ApiService.instance.postMultipart('/patient/chatbot/audio',
      fields: {
        'langue':     _langue,
        'session_id': ?_sessionId,
      },
      files: {'audio': path},
    );
    if (!mounted) return;

    if (res.ok) {
      _sessionId = res.data['session_id'] as String? ?? _sessionId;
      final transcription = res.data['transcription'] as String?;
      final reponse = _Msg(
        text:     res.data['message']?['message'] as String? ?? '…',
        isUser:   false,
        audioUrl: res.data['audio_url'] as String?,
      );
      setState(() {
        if (transcription != null && transcription.isNotEmpty) {
          _messages.add(_Msg(text: transcription, isUser: true));
        }
        _messages.add(reponse);
        _loading = false;
      });
      if (_voiceOn) _parler(reponse);
    } else {
      setState(() => _loading = false);
      _snack(res.error ?? 'Service vocal indisponible.');
    }
    _scrollToBottom();
  }

  void _snack(String m) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(m), behavior: SnackBarBehavior.floating));

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scroll.hasClients) {
        _scroll.animateTo(_scroll.position.maxScrollExtent,
          duration: const Duration(milliseconds: 300), curve: Curves.easeOut);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        titleSpacing: 0,
        title: Row(
          children: [
            const CircleAvatar(
              radius: 18,
              backgroundColor: AppColors.primarySoft,
              child: Icon(Icons.smart_toy_rounded, color: AppColors.primary, size: 20),
            ),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Assistant IA YOONU JIGEEN',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.ink)),
                Row(children: [
                  Container(width: 7, height: 7,
                    decoration: const BoxDecoration(color: AppColors.success, shape: BoxShape.circle)),
                  const SizedBox(width: 4),
                  const Text('En ligne', style: TextStyle(fontSize: 11, color: AppColors.success)),
                ]),
              ],
            ),
          ],
        ),
        actions: [
          // ── Bascule langue FR / WO ──────────────────────────────────────
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 2),
            child: GestureDetector(
              onTap: _toggleLangue,
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: _langue == 'wo' ? AppColors.primary : AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: _langue == 'wo' ? AppColors.primary : AppColors.border,
                    width: 1.5,
                  ),
                ),
                child: Text(
                  _langue == 'wo' ? '🇸🇳 WO' : '🇫🇷 FR',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: _langue == 'wo' ? Colors.white : AppColors.primary,
                  ),
                ),
              ),
            ),
          ),
          // ── Volume ──────────────────────────────────────────────────────
          IconButton(
            icon: Icon(
              _voiceOn ? Icons.volume_up_rounded : Icons.volume_off_rounded,
              color: _voiceOn ? AppColors.primary : AppColors.ink2,
            ),
            tooltip: _voiceOn ? 'Couper la voix' : 'Activer la voix',
            onPressed: () {
              setState(() => _voiceOn = !_voiceOn);
              if (!_voiceOn) _tts.stop();
            },
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline_rounded, color: AppColors.ink2),
            tooltip: 'Effacer la conversation',
            onPressed: () {
              _tts.stop();
              setState(() => _messages.removeRange(1, _messages.length));
            },
          ),
        ],
      ),
      body: Column(
        children: [
          _QuickSuggestions(onTap: _send, langue: _langue),
          Expanded(
            child: ListView.builder(
              controller: _scroll,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              itemCount: _messages.length + (_loading ? 1 : 0),
              itemBuilder: (_, i) {
                if (i == _messages.length) return const _TypingBubble();
                final m = _messages[i];
                return _MessageBubble(
                  msg: m,
                  // Toute réponse de l'assistant peut être écoutée (audio serveur ou synthèse vocale).
                  onPlay: m.isUser ? null : () => _parler(m),
                );
              },
            ),
          ),
          _InputBar(
            controller: _ctrl,
            recording: _recording,
            langue:    _langue,
            onSend: () => _send(),
            onMic:  _toggleRecord,
          ),
        ],
      ),
      bottomNavigationBar: const BottomNav(currentIndex: 2),
    );
  }
}

// ─── MODÈLE MESSAGE ──────────────────────────────────────────────────────────
class _Msg {
  final String   text;
  final bool     isUser;
  final String?  audioUrl;
  final DateTime time;
  _Msg({required this.text, required this.isUser, this.audioUrl}) : time = DateTime.now();
}

// ─── BULLE DE MESSAGE ────────────────────────────────────────────────────────
class _MessageBubble extends StatelessWidget {
  final _Msg msg;
  final VoidCallback? onPlay;
  const _MessageBubble({required this.msg, this.onPlay});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: msg.isUser ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
        margin: const EdgeInsets.only(bottom: 14),
        child: Column(
          crossAxisAlignment:
              msg.isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
          children: [
            if (!msg.isUser)
              const Padding(
                padding: EdgeInsets.only(left: 4, bottom: 5),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  CircleAvatar(
                    radius: 11,
                    backgroundColor: AppColors.primarySoft,
                    child: Icon(Icons.smart_toy_rounded, color: AppColors.primary, size: 13),
                  ),
                  SizedBox(width: 5),
                  Text('Assistant',
                    style: TextStyle(fontSize: 11, color: AppColors.ink2, fontWeight: FontWeight.w500)),
                ]),
              ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: msg.isUser ? AppColors.primary : AppColors.surface,
                borderRadius: BorderRadius.only(
                  topLeft:     const Radius.circular(18),
                  topRight:    const Radius.circular(18),
                  bottomLeft:  Radius.circular(msg.isUser ? 18 : 4),
                  bottomRight: Radius.circular(msg.isUser ? 4 : 18),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.06),
                    blurRadius: 8, offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(msg.text,
                    style: TextStyle(
                      fontSize: 13.5,
                      color: msg.isUser ? Colors.white : AppColors.ink,
                      height: 1.55,
                    )),
                  if (onPlay != null) ...[
                    const SizedBox(height: 6),
                    GestureDetector(
                      onTap: onPlay,
                      child: Row(mainAxisSize: MainAxisSize.min, children: const [
                        Icon(Icons.volume_up_rounded, size: 16, color: AppColors.primary),
                        SizedBox(width: 4),
                        Text('Écouter', style: TextStyle(fontSize: 12,
                          fontWeight: FontWeight.w700, color: AppColors.primary)),
                      ]),
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 3),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 4),
              child: Text(
                '${msg.time.hour.toString().padLeft(2,'0')}:${msg.time.minute.toString().padLeft(2,'0')}',
                style: const TextStyle(fontSize: 10, color: AppColors.ink2),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─── INDICATEUR "EN TRAIN D'ÉCRIRE" ─────────────────────────────────────────
class _TypingBubble extends StatelessWidget {
  const _TypingBubble();
  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(18), topRight: Radius.circular(18),
            bottomLeft: Radius.circular(4), bottomRight: Radius.circular(18),
          ),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 8)],
        ),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [
          _AnimDot(delay: 0),   SizedBox(width: 5),
          _AnimDot(delay: 180), SizedBox(width: 5),
          _AnimDot(delay: 360),
        ]),
      ),
    );
  }
}

class _AnimDot extends StatefulWidget {
  final int delay;
  const _AnimDot({required this.delay});
  @override State<_AnimDot> createState() => _AnimDotState();
}
class _AnimDotState extends State<_AnimDot> with SingleTickerProviderStateMixin {
  late AnimationController _ac;
  late Animation<double>   _fade;
  @override
  void initState() {
    super.initState();
    _ac   = AnimationController(vsync: this, duration: const Duration(milliseconds: 700));
    _fade = Tween(begin: 0.25, end: 1.0).animate(CurvedAnimation(parent: _ac, curve: Curves.easeInOut));
    Future.delayed(Duration(milliseconds: widget.delay), () { if (mounted) _ac.repeat(reverse: true); });
  }
  @override void dispose() { _ac.dispose(); super.dispose(); }
  @override
  Widget build(BuildContext context) => FadeTransition(
    opacity: _fade,
    child: Container(width: 8, height: 8,
      decoration: const BoxDecoration(color: AppColors.primary, shape: BoxShape.circle)),
  );
}

// ─── SUGGESTIONS RAPIDES ─────────────────────────────────────────────────────
class _QuickSuggestions extends StatelessWidget {
  final ValueChanged<String> onTap;
  final String langue;
  const _QuickSuggestions({required this.onTap, required this.langue});

  static const _itemsFr = [
    ('🤰', 'Combien de consultations prénatales ?'),
    ('🩸', 'Signes de danger pendant la grossesse'),
    ('🍎', 'Alimentation pendant la grossesse'),
    ('💉', 'Vaccins de la grossesse'),
    ('👶', 'Mouvements du bébé'),
  ];

  static const _itemsWo = [
    ('🤰', 'Ñaata wisite prénatale la war a def ?'),
    ('🩸', 'Yan mooy signou urgence ci ëmbë ?'),
    ('🍎', 'Lan lañu war lekk ci biir ëmbë ?'),
    ('💉', 'Ñaata pikir tatanos la war a jël ?'),
    ('👶', 'Lan mooy signes du travail ?'),
  ];

  @override
  Widget build(BuildContext context) {
    final items = langue == 'wo' ? _itemsWo : _itemsFr;
    return Container(
      height: 46,
      color: AppColors.surface,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
        itemCount: items.length,
        separatorBuilder: (_, i) => const SizedBox(width: 8),
        itemBuilder: (_, i) => GestureDetector(
          onTap: () => onTap(items[i].$2),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            decoration: BoxDecoration(
              color: AppColors.primarySoft,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: AppColors.border),
            ),
            alignment: Alignment.center,
            child: Text('${items[i].$1} ${items[i].$2}',
              style: const TextStyle(fontSize: 12, color: AppColors.primary, fontWeight: FontWeight.w500)),
          ),
        ),
      ),
    );
  }
}

// ─── BARRE DE SAISIE (texte + micro) ─────────────────────────────────────────
class _InputBar extends StatelessWidget {
  final TextEditingController controller;
  final bool recording;
  final String langue;
  final VoidCallback onSend;
  final VoidCallback onMic;
  const _InputBar({
    required this.controller,
    required this.recording,
    required this.langue,
    required this.onSend,
    required this.onMic,
  });

  @override
  Widget build(BuildContext context) {
    final hintText = recording
        ? 'Enregistrement en cours…'
        : (langue == 'wo' ? 'Laajal sama… (ëmbii wala menopause)' : 'Posez votre question…');
    return Container(
      padding: const EdgeInsets.fromLTRB(14, 8, 14, 16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        boxShadow: [BoxShadow(
          color: AppColors.primary.withValues(alpha: 0.06),
          blurRadius: 16, offset: const Offset(0, -4),
        )],
      ),
      child: SafeArea(
        top: false,
        child: Row(children: [
          Expanded(
            child: TextField(
              controller: controller,
              maxLines: null,
              textInputAction: TextInputAction.send,
              onSubmitted: (_) => onSend(),
              decoration: InputDecoration(
                hintText: hintText,
                hintStyle: TextStyle(
                  color: recording ? AppColors.danger : AppColors.ink2, fontSize: 13),
                filled: true,
                fillColor: AppColors.bg,
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(24),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(24),
                  borderSide: const BorderSide(color: AppColors.border),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(24),
                  borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
                ),
              ),
            ),
          ),
          const SizedBox(width: 8),
          // Bouton micro (passe en rouge pendant l'enregistrement)
          Material(
            color: recording ? AppColors.danger : AppColors.primarySoft,
            borderRadius: BorderRadius.circular(24),
            child: InkWell(
              onTap: onMic,
              borderRadius: BorderRadius.circular(24),
              child: Padding(
                padding: const EdgeInsets.all(11),
                child: Icon(recording ? Icons.stop_rounded : Icons.mic_rounded,
                  color: recording ? Colors.white : AppColors.primary, size: 20),
              ),
            ),
          ),
          const SizedBox(width: 8),
          Material(
            color: AppColors.primary,
            borderRadius: BorderRadius.circular(24),
            child: InkWell(
              onTap: onSend,
              borderRadius: BorderRadius.circular(24),
              child: const Padding(
                padding: EdgeInsets.all(11),
                child: Icon(Icons.send_rounded, color: Colors.white, size: 20),
              ),
            ),
          ),
        ]),
      ),
    );
  }
}
