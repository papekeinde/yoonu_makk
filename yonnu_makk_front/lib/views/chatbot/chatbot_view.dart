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

  final List<_Msg> _messages = [];

  @override
  void initState() {
    super.initState();
    _messages.add(_Msg(text: _msgAccueilFr, isUser: false));
    _initTts();
    _chargerHistorique();
  }

  Future<void> _initTts() async {
    try {
      final moteurs = (await _tts.getEngines) as List?;
      if (moteurs != null && moteurs.contains('com.google.android.tts')) {
        await _tts.setEngine('com.google.android.tts');
      }
    } catch (_) {}

    await _tts.setLanguage('fr-FR');
    await _choisirMeilleureVoixFr();

    await _tts.setSpeechRate(0.46);
    await _tts.setPitch(1.05);
    await _tts.setVolume(1.0);
    await _tts.awaitSpeakCompletion(true);
  }

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
        if (n.contains('network')) s += 4;
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
    } catch (_) {}
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

  Future<void> _parler(_Msg m) async {
    await _tts.stop();
    await _player.stop();
    if (m.audioUrl != null) {
      try { await _player.play(UrlSource(m.audioUrl!)); return; } catch (_) {}
    }
    if (_langue == 'wo') return;
    final texte = m.text.trim();
    if (texte.isNotEmpty) {
      try { await _tts.speak(texte); } catch (_) {}
    }
  }

  void _toggleLangue() {
    _tts.stop();
    _player.stop();
    setState(() {
      _langue = _langue == 'fr' ? 'wo' : 'fr';
      _messages.clear();
      _messages.add(_Msg(
            text: _langue == 'wo' ? _msgAccueilWo : _msgAccueilFr,
            isUser: false));
      _sessionId = null;
    });
    _chargerHistorique();
  }

  Future<void> _chargerHistorique() async {
    final res = await ApiService.instance.get('/patient/chatbot/historique');
    if (!mounted || !res.ok) return;
    final data = (res.data['data'] as List? ?? []);
    if (data.isEmpty) return;
    
    final items = data.reversed
        .map((e) => _Msg(
              text:   e['message'] as String? ?? '',
              isUser: e['role'] == 'utilisateur',
            ))
        .toList();
    setState(() {
      _messages.clear();
      _messages.addAll(items);
      _sessionId = data.first['session_id'] as String?;
    });
    _scrollToBottom();
  }

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
      'session_id': _sessionId,
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
        'session_id': _sessionId ?? '',
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
    Future.delayed(const Duration(milliseconds: 100), () {
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
            const SizedBox(width: 8),
            const CircleAvatar(
              radius: 18,
              backgroundColor: AppColors.primarySoft,
              child: Icon(Icons.smart_toy_rounded, color: AppColors.primary, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Assistant Santé',
                  style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppColors.ink)),
                Text('En ligne', style: TextStyle(fontSize: 11, color: AppColors.success)),
              ],
            ),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
            child: GestureDetector(
              onTap: _toggleLangue,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: _langue == 'wo' ? AppColors.primary : AppColors.primarySoft,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  _langue == 'wo' ? 'WO' : 'FR',
                  style: TextStyle(
                    fontSize: 12, fontWeight: FontWeight.w700,
                    color: _langue == 'wo' ? Colors.white : AppColors.primary,
                  ),
                ),
              ),
            ),
          ),
          IconButton(
            icon: Icon(
              _voiceOn ? Icons.volume_up_rounded : Icons.volume_off_rounded,
              color: _voiceOn ? AppColors.primary : AppColors.ink2, size: 20,
            ),
            onPressed: () {
              setState(() => _voiceOn = !_voiceOn);
              if (!_voiceOn) { _tts.stop(); _player.stop(); }
            },
          ),
          const SizedBox(width: 4),
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
    );
  }
}

class _Msg {
  final String   text;
  final bool     isUser;
  final String?  audioUrl;
  final DateTime time;
  _Msg({required this.text, required this.isUser, this.audioUrl}) : time = DateTime.now();
}

class _MessageBubble extends StatelessWidget {
  final _Msg msg;
  final VoidCallback? onPlay;
  const _MessageBubble({required this.msg, this.onPlay});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: msg.isUser ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.8),
        margin: const EdgeInsets.only(bottom: 12),
        child: Column(
          crossAxisAlignment: msg.isUser ? CrossAxisAlignment.end : CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: msg.isUser ? AppColors.primary : Colors.white,
                borderRadius: BorderRadius.circular(16).copyWith(
                  bottomRight: msg.isUser ? const Radius.circular(0) : null,
                  bottomLeft: !msg.isUser ? const Radius.circular(0) : null,
                ),
                boxShadow: [
                  BoxShadow(color: Colors.black.withValues(alpha: .04), blurRadius: 4, offset: const Offset(0, 2))
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(msg.text,
                    style: TextStyle(
                      fontSize: 14,
                      color: msg.isUser ? Colors.white : AppColors.ink,
                    )),
                  if (onPlay != null) ...[
                    const SizedBox(height: 8),
                    InkWell(
                      onTap: onPlay,
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.play_circle_fill_rounded, size: 20, color: msg.isUser ? Colors.white : AppColors.primary),
                          const SizedBox(width: 6),
                          Text('Écouter', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: msg.isUser ? Colors.white : AppColors.primary)),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 4),
            Text(
              '${msg.time.hour}:${msg.time.minute.toString().padLeft(2, '0')}',
              style: const TextStyle(fontSize: 10, color: AppColors.ink2),
            ),
          ],
        ),
      ),
    );
  }
}

class _TypingBubble extends StatelessWidget {
  const _TypingBubble();
  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16).copyWith(bottomLeft: const Radius.circular(0)),
        ),
        child: const Row(mainAxisSize: MainAxisSize.min, children: [
          _AnimDot(delay: 0),   SizedBox(width: 4),
          _AnimDot(delay: 200), SizedBox(width: 4),
          _AnimDot(delay: 400),
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
    _ac = AnimationController(vsync: this, duration: const Duration(milliseconds: 600));
    _fade = Tween(begin: 0.3, end: 1.0).animate(_ac);
    Future.delayed(Duration(milliseconds: widget.delay), () { if (mounted) _ac.repeat(reverse: true); });
  }
  @override void dispose() { _ac.dispose(); super.dispose(); }
  @override
  Widget build(BuildContext context) => FadeTransition(opacity: _fade,
    child: Container(width: 6, height: 6, decoration: const BoxDecoration(color: AppColors.primary, shape: BoxShape.circle)));
}

class _QuickSuggestions extends StatelessWidget {
  final ValueChanged<String> onTap;
  final String langue;
  const _QuickSuggestions({required this.onTap, required this.langue});

  @override
  Widget build(BuildContext context) {
    final items = langue == 'wo' 
      ? [('🤰', 'Ñaata wisite prénatale ?'), ('🩸', 'Signou urgence ?'), ('🍎', 'Lekk ci biir ëmbë ?')]
      : [('🤰', 'Consultations ?'), ('🩸', 'Signes de danger ?'), ('🍎', 'Alimentation ?')];
    
    return Container(
      height: 48,
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (_, i) => ActionChip(
          label: Text('${items[i].$1} ${items[i].$2}', style: const TextStyle(fontSize: 12)),
          onPressed: () => onTap(items[i].$2),
          backgroundColor: AppColors.surface,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          side: const BorderSide(color: AppColors.border),
        ),
      ),
    );
  }
}

class _InputBar extends StatelessWidget {
  final TextEditingController controller;
  final bool recording;
  final String langue;
  final VoidCallback onSend, onMic;
  const _InputBar({required this.controller, required this.recording, required this.langue, required this.onSend, required this.onMic});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.fromLTRB(16, 8, 16, MediaQuery.of(context).padding.bottom + 8),
      decoration: BoxDecoration(
        color: AppColors.surface,
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: .04), blurRadius: 10, offset: const Offset(0, -2))],
      ),
      child: Row(children: [
        Expanded(
          child: TextField(
            controller: controller,
            maxLines: null,
            decoration: InputDecoration(
              hintText: recording ? 'J\'écoute...' : 'Posez une question...',
              hintStyle: TextStyle(color: recording ? AppColors.danger : AppColors.ink2),
              border: InputBorder.none,
              enabledBorder: InputBorder.none,
              focusedBorder: InputBorder.none,
              filled: false,
            ),
          ),
        ),
        IconButton(
          icon: Icon(recording ? Icons.stop_circle_rounded : Icons.mic_none_rounded, 
            color: recording ? AppColors.danger : AppColors.primary, size: 28),
          onPressed: onMic,
        ),
        IconButton(
          icon: const Icon(Icons.send_rounded, color: AppColors.primary),
          onPressed: onSend,
        ),
      ]),
    );
  }
}
