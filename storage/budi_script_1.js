
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    },
                    colors: {
                        canvas: '#F4F4F0',
                        obsidian: '#0B0F14',
                        carbon: '#121820',
                        surface: '#FFFFFF',
                        subtle: '#F8F8F6',
                        ink: '#0F1419',
                        muted: '#53606E',
                        accent: '#0D9488',
                        emerald: {
                            400: '#34D399',
                            500: '#10B981',
                            600: '#059669',
                        }
                    },
                    transitionTimingFunction: {
                        'spring': 'cubic-bezier(0.32, 0.72, 0, 1)',
                    }
                }
            }
        }
    