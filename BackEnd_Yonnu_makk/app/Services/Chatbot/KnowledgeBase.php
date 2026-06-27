<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Str;

/**
 * Repli local : recherche par mots-clés dans new_knowledge.json
 * (base bilingue wolof/français) quand l'API externe est injoignable.
 */
class KnowledgeBase
{
    private ?array $questions = null;

    private function load(): array
    {
        if ($this->questions !== null) {
            return $this->questions;
        }

        $path = resource_path('chatbot/new_knowledge.json');
        if (! is_file($path)) {
            return $this->questions = [];
        }

        $data = json_decode((string) file_get_contents($path), true);

        return $this->questions = $data['questions'] ?? [];
    }

    /**
     * Retourne la meilleure réponse trouvée, ou null si rien ne correspond.
     */
    public function search(string $message, string $langue = 'fr'): ?string
    {
        $questions = $this->load();
        if (empty($questions)) {
            return null;
        }

        $tokens = $this->tokenize($message);
        if (empty($tokens)) {
            return null;
        }

        $meilleur = null;
        $meilleurScore = 0;

        foreach ($questions as $q) {
            $score = $this->score($q, $tokens);
            if ($score > $meilleurScore) {
                $meilleurScore = $score;
                $meilleur = $q;
            }
        }

        // Seuil minimal pour éviter les faux positifs.
        if ($meilleur === null || $meilleurScore < 2) {
            return null;
        }

        return $this->reponsePour($meilleur, $langue);
    }

    private function score(array $q, array $tokens): int
    {
        $keywords = array_merge(
            $q['keywords_fr'] ?? [],
            $q['keywords_wo'] ?? [],
            $q['keywords_common'] ?? [],
        );

        $score = 0;
        foreach ($keywords as $kw) {
            $kwNorm = $this->normalize($kw);
            if ($kwNorm === '') {
                continue;
            }
            foreach ($tokens as $t) {
                if ($t === $kwNorm || str_contains($t, $kwNorm) || str_contains($kwNorm, $t)) {
                    $score++;
                    break;
                }
            }
        }

        // Bonus si une bonne partie de la question correspond.
        $questionNorm = $this->normalize(($q['question_fr'] ?? '') . ' ' . ($q['question_wo'] ?? ''));
        foreach ($tokens as $t) {
            if (strlen($t) >= 4 && str_contains($questionNorm, $t)) {
                $score++;
            }
        }

        // Les concepts importants priment légèrement.
        if (($q['importance'] ?? '') === 'high') {
            $score += 0;
        }

        return $score;
    }

    private function reponsePour(array $q, string $langue): string
    {
        if ($langue === 'wo') {
            return $q['response_wo'] ?? $q['answer_bilingual'] ?? $q['response_fr'] ?? '';
        }

        return $q['response_fr'] ?? $q['answer_bilingual'] ?? $q['response_wo'] ?? '';
    }

    /** Découpe + normalise en mots significatifs (>= 3 lettres). */
    private function tokenize(string $message): array
    {
        $norm = $this->normalize($message);
        $mots = preg_split('/\s+/', $norm, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter($mots, fn ($m) => strlen($m) >= 3));
    }

    private function normalize(string $s): string
    {
        $s = Str::lower(trim($s));
        $s = strtr($s, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ]);

        return (string) preg_replace('/[^a-z0-9\s]/', ' ', $s);
    }
}
