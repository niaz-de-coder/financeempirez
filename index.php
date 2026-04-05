<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Empirez | Automated Accounting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px); }
        .hero-gradient { background: radial-gradient(circle at top right, #eff6ff 0%, #ffffff 50%); }
    </style>
</head>
<body class="bg-white text-slate-900 selection:bg-blue-100 selection:text-blue-700">

    <nav class="fixed w-full z-50 transition-all duration-500 border-b border-transparent" id="navbar">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-xl font-extrabold tracking-tighter flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-sm">F</div>
                <span>FINANCE<span class="text-blue-600">Empirez</span></span>
            </div>
            
            <div class="hidden md:flex items-center space-x-10 text-sm font-semibold text-slate-500">
                <a href="#features" class="hover:text-blue-600 transition-colors">Features</a>
                <a href="#about" class="hover:text-blue-600 transition-colors">About</a>
                <a href="#contact" class="hover:text-blue-600 transition-colors">Contact</a>
                <a href="signup.php" class="bg-slate-900 text-white px-6 py-2.5 rounded-full hover:bg-blue-600 transition-all shadow-xl shadow-slate-200">Get Started</a>
            </div>
        </div>
    </nav>

    <section class="hero-gradient min-h-screen flex items-center pt-20">
        <div class="container mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 mb-6 text-xs font-bold tracking-widest text-blue-600 uppercase bg-blue-50 rounded-full">New: Automated Ledger v2.0</span>
                <h1 class="text-6xl lg:text-8xl font-extrabold tracking-tight mb-8 leading-[1.1]">
                    Accounting on <span class="text-blue-600">Autopilot.</span>
                </h1>
                <p class="text-xl text-slate-500 mb-10 leading-relaxed max-w-lg">
                    Stop wrestling with spreadsheets. Input your daily journal entries and watch your balance sheets and P&L statements generate instantly.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#features" class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-2xl shadow-blue-200">Explore Features</a>
                    <a href="demo.php" class="px-8 py-4 border border-slate-200 rounded-2xl font-bold hover:bg-slate-50 transition">View Live Demo</a>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse"></div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.1)] border border-slate-100 relative z-10">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="h-4 w-32 bg-slate-100 rounded"></div>
                            <div class="h-8 w-8 bg-blue-50 rounded-full"></div>
                        </div>
                        <div class="h-32 w-full bg-slate-50 rounded-2xl border-2 border-dashed border-slate-100 flex items-center justify-center">
                            <p class="text-slate-400 text-sm font-medium">Auto-generating Financial Chart...</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-20 bg-blue-50 rounded-2xl p-4">
                                <div class="h-2 w-12 bg-blue-200 rounded mb-2"></div>
                                <div class="h-4 w-20 bg-blue-400 rounded"></div>
                            </div>
                            <div class="h-20 bg-emerald-50 rounded-2xl p-4">
                                <div class="h-2 w-12 bg-emerald-200 rounded mb-2"></div>
                                <div class="h-4 w-20 bg-emerald-400 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-32 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-4xl font-bold mb-6">Powerful, Yet Simple</h2>
                <p class="text-slate-500 text-lg">We took the complexity out of accounting so you can focus on growing your business or managing your .</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-10 rounded-3xl border border-slate-100 hover:shadow-2xl transition duration-500">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-blue-100">
                        <i class="fa-solid fa-bolt-lightning text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Real-time Sync</h3>
                    <p class="text-slate-500 leading-relaxed">Your financial position is updated the second you hit 'Save' on a journal entry.</p>
                </div>
                <div class="bg-white p-10 rounded-3xl border border-slate-100 hover:shadow-2xl transition duration-500">
                    <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-emerald-100">
                        <i class="fa-solid fa-file-pdf text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Smart Export</h3>
                    <p class="text-slate-500 leading-relaxed">Generate professional PDF reports for tax season or board meetings with one click.</p>
                </div>
                <div class="bg-white p-10 rounded-3xl border border-slate-100 hover:shadow-2xl transition duration-500">
                    <div class="w-14 h-14 bg-purple-500 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-purple-100">
                        <i class="fa-solid fa-lock text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Bank-Level Security</h3>
                    <p class="text-slate-500 leading-relaxed">Your data is encrypted. We ensure your financial history stays private and secure.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-32 overEmpirez-hidden">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-20 items-center">
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=1000" alt="Finance" class="rounded-[3rem] shadow-2xl">
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-4xl font-bold mb-8 italic text-blue-600">"Accounting shouldn't be a chore."</h2>
                    <p class="text-xl text-slate-600 leading-relaxed mb-8">
                        Finance Empirez was built on a simple premise: Most people understand their transactions, but few enjoy the math behind them.
                    </p>
                    <p class="text-slate-500 leading-relaxed mb-10">
                        Our platform acts as your digital bookkeeper. By focusing solely on the Double Entry system (Journals), we automate the ledger, trial balance, and financial statements, giving you professional-grade insights without the professional-grade price tag.
                    </p>
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-3xl font-bold">99.9%</p>
                            <p class="text-sm text-slate-400">Accuracy Rate</p>
                        </div>
                        <div class="h-10 w-px bg-slate-200"></div>
                        <div>
                            <p class="text-3xl font-bold">24/7</p>
                            <p class="text-sm text-slate-400">Cloud Access</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-32 bg-slate-900 text-white rounded-[4rem] mx-4 mb-20">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-8">Ready to take control?</h2>
            <p class="text-slate-400 mb-12 max-w-xl mx-auto">Have questions about our automation? Our team is here to help you transition your accounts today.</p>
            
            <form class="max-w-md mx-auto space-y-4">
                <input type="email" placeholder="Enter your email address" class="w-full px-6 py-4 bg-slate-800 border border-slate-700 rounded-2xl focus:outline-none focus:border-blue-500 transition">
                <button class="w-full py-4 bg-blue-600 rounded-2xl font-bold hover:bg-blue-700 transition">Contact Support</button>
            </form>
            
            <div class="mt-16 pt-16 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-sm text-slate-500">© 2026 Finance Empirez. Built for the future of finance.</div>
                <div class="flex gap-6 text-slate-400">
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-github"></i></a>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('glass', 'py-3', 'border-slate-100');
                nav.classList.remove('py-4', 'border-transparent');
            } else {
                nav.classList.remove('glass', 'py-3', 'border-slate-100');
                nav.classList.add('py-4', 'border-transparent');
            }
        });
    </script>
</body>
</html>