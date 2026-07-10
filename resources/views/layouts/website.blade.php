<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="YOONU JIGEEN aide à l'orientation, à la coordination et au suivi gynécologique sans se substituer au diagnostic médical.">
    <title>@yield('title', 'YOONU JIGEEN')</title>
    <link rel="icon" href="{{ asset('logo-yoonu-makk.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        .animate-fade-in-left {
            animation: fadeInLeft 0.8s ease-out;
        }
        .animate-fade-in-right {
            animation: fadeInRight 0.8s ease-out;
        }
        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .animate-delay-400 { animation-delay: 0.4s; }
        .animate-delay-500 { animation-delay: 0.5s; }
        .animate-delay-600 { animation-delay: 0.6s; }
        .layout-morph-section {
            position: relative;
            isolation: isolate;
        }
        .layout-morph-section::before {
            content: '';
            position: absolute;
            inset: 1.25rem auto auto 1.25rem;
            width: 7rem;
            height: 7rem;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(233, 30, 99, 0.18) 0%, rgba(233, 30, 99, 0) 72%);
            pointer-events: none;
            z-index: -1;
        }
        .layout-morph-card,
        .layout-morph-accent {
            transform-origin: center;
            will-change: transform, opacity, box-shadow, border-radius;
        }

        /* ===== Bandeau défilant (marquee) ===== */
        @keyframes marqueeScroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .marquee-track {
            display: flex;
            width: max-content;
            animation: marqueeScroll 30s linear infinite;
        }
        .marquee-wrap:hover .marquee-track { animation-play-state: paused; }

        /* ===== Scrollbar & sélection aux couleurs de la charte ===== */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #140509; }
        ::-webkit-scrollbar-thumb { background: #AD1457; border-radius: 6px; }
        ::-webkit-scrollbar-thumb:hover { background: #E91E63; }
        ::selection { background: rgba(233, 30, 99, 0.32); color: #fff; }

        /* ===== Décalage d'ancrage sous la navbar fixe ===== */
        section[id], [id] { scroll-margin-top: 88px; }

        /* ===== Alpine.js : masquer avant initialisation ===== */
        [x-cloak] { display: none !important; }

        html { scroll-behavior: smooth; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'ui-sans-serif', 'system-ui'],
                        display: ['Archivo', 'ui-sans-serif', 'system-ui'],
                        serif: ['Fraunces', 'Georgia', 'Times New Roman', 'serif']
                    },
                    colors: {
                        yoonu: {
                            50: '#FCE4EC',
                            100: '#F8BBD0',
                            200: '#F48FB1',
                            300: '#F06292',
                            400: '#EC407A',
                            500: '#E91E63',
                            600: '#C2185B',
                            700: '#AD1457',
                            800: '#8B1A47',
                            900: '#6B0D2B'
                        },
                        // Échelle de triage — palette sémantique d'urgence,
                        // sourde et accordée au rose de marque.
                        urgence: {
                            50: '#FAEEEC', 100: '#F1D5D0', 200: '#E3ADA4',
                            500: '#BC4A3C', 600: '#A53E32', 700: '#8A3328'
                        },
                        rapide: {
                            50: '#FAF3E4', 100: '#F0E1BC', 200: '#E2C988',
                            500: '#C08A33', 600: '#A37223', 700: '#855C1C'
                        },
                        standard: {
                            50: '#EDF2ED', 100: '#D6E2D6', 200: '#B0C7B2',
                            500: '#5F8568', 600: '#4E7056', 700: '#3E5A45'
                        },
                        // Thème sombre — repris du dashboard (sidebar bordeaux).
                        ink: {
                            950: '#140509', // fond le plus profond
                            900: '#1A0710', // fond de page (= sidebar dashboard)
                            800: '#25101A', // carte (= dark-card)
                            700: '#311624', // surface secondaire / hover
                            600: '#3A1A28'  // bordure (= dark-border)
                        },
                        mauve: {
                            200: '#DCC9D2', // texte clair
                            300: '#C9B8C0', // texte courant
                            400: '#A98F99', // texte atténué
                            500: '#866974'  // texte discret
                        }
                    },
                    boxShadow: {
                        soft: '0 8px 24px rgba(15, 23, 42, 0.06)'
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300;1,9..144,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script>
        gsap.registerPlugin(ScrollTrigger);
    </script>
</head>
<body class="font-sans bg-ink-900 text-mauve-300 min-h-screen antialiased">
    @yield('content')
    <script>
        // GSAP Scroll Reveal Animations
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll reveal pour les cartes
            gsap.utils.toArray('.scroll-reveal').forEach(el => {
                gsap.from(el, {
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 80%',
                        end: 'top 50%',
                        scrub: false,
                        markers: false
                    },
                    opacity: 0,
                    y: 40,
                    duration: 0.6,
                    ease: 'power2.out'
                });
            });

            // Layout morph - cards hover effect
            gsap.utils.toArray('.morph-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    gsap.to(this, {
                        scale: 1.05,
                        boxShadow: '0 20px 40px rgba(169, 20, 87, 0.15)',
                        duration: 0.3,
                        ease: 'power2.out'
                    });
                });
                card.addEventListener('mouseleave', function() {
                    gsap.to(this, {
                        scale: 1,
                        boxShadow: '0 8px 24px rgba(15, 23, 42, 0.06)',
                        duration: 0.3,
                        ease: 'power2.out'
                    });
                });
            });

            // Scroll-triggered image zoom
            gsap.utils.toArray('.image-zoom').forEach(img => {
                gsap.from(img, {
                    scrollTrigger: {
                        trigger: img,
                        start: 'top 70%',
                        end: 'top 30%',
                        scrub: 1,
                        markers: false
                    },
                    scale: 0.95,
                    opacity: 0.8,
                    duration: 0.6
                });
            });

            // Staggered list animation
            gsap.utils.toArray('.stagger-item').forEach((item, index) => {
                gsap.from(item, {
                    scrollTrigger: {
                        trigger: item.closest('ul, .grid, [data-stagger]'),
                        start: 'top 75%'
                    },
                    opacity: 0,
                    x: -20,
                    duration: 0.5,
                    delay: index * 0.1,
                    ease: 'back.out(1.7)'
                });
            });

            // Counter animation
            gsap.utils.toArray('.counter').forEach(counter => {
                let target = parseFloat(counter.getAttribute('data-target') || counter.textContent);
                gsap.to(counter, {
                    scrollTrigger: {
                        trigger: counter,
                        start: 'top 80%'
                    },
                    innerText: Math.floor(target),
                    duration: 2,
                    ease: 'power2.out',
                    snap: { innerText: 1 }
                });
            });

            gsap.utils.toArray('.layout-morph-section').forEach(section => {
                const intro = section.querySelector('.layout-morph-intro');
                const accent = section.querySelector('.layout-morph-accent');
                const cards = section.querySelectorAll('.layout-morph-card');

                const timeline = gsap.timeline({
                    scrollTrigger: {
                        trigger: section,
                        start: 'top 72%',
                        once: true
                    }
                });

                if (intro) {
                    timeline.from(intro, {
                        opacity: 0,
                        y: 26,
                        duration: 0.55,
                        ease: 'power2.out'
                    });
                }

                if (accent) {
                    timeline.from(accent, {
                        opacity: 0,
                        x: 36,
                        scale: 0.94,
                        borderRadius: '2rem',
                        duration: 0.6,
                        ease: 'power2.out'
                    }, '-=0.28');
                }

                if (cards.length) {
                    timeline.from(cards, {
                        opacity: 0,
                        y: 42,
                        scale: 0.92,
                        borderRadius: '2rem',
                        stagger: 0.09,
                        duration: 0.55,
                        ease: 'power2.out'
                    }, '-=0.16');
                }
            });
        });
    </script>
</body>
</html>
