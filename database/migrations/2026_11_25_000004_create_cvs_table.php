<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row holding the CV shown at /cv and edited at /cv/edit (admins only).
     * Seeded with the CV written in November 2026.
     */
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->timestamps();
        });

        DB::table('cvs')->insert([
            'data' => json_encode(self::seed(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }

    private static function seed(): array
    {
        return [
            'name' => 'Alex Davies',
            'headline' => 'Full-stack developer',
            'location' => 'Stoke-on-Trent',
            'email' => 'me@alexdavi.es',
            'phone' => '07534 708314',
            'show_phone' => false,
            'website' => 'https://alexdavi.es',
            'github' => 'https://github.com/Sinclair8700',
            'extra' => 'Full UK driving licence',
            'profile' => <<<'MD'
I'm a full-stack developer with a First in Computer Science from Keele University. I like owning a project end to end: the database, the back end, the front end, and the Linux server it runs on.

Since October 2022 I've worked at Designamite, where I'm development lead for FibreNest, Persimmon Homes' fibre broadband business. I've also helped build Persimmon's site management app, which tracks around 400,000 plots for 20,000 users. On the side I build and host websites and online shops for local businesses under [21cs.co.uk](https://21cs.co.uk). Outside client work I go after the harder problems, like a 16-bit computer that runs in the browser, a Raspberry Pi that turns my PC on and off from anywhere, and a 32,000-line Godot game with online co-op.

I started out writing small desktop tools in C++ and C#, like always-on-top media controls and an AES-encrypted password manager, and most of my projects still start with something I want to fix or automate. I'm looking for a role where I can keep leading projects and take on harder technical problems.
MD,
            'skills' => <<<'TXT'
Languages: PHP, JavaScript, TypeScript, Python, C++, C#, Java, SQL, GDScript, HTML, CSS
Web: Laravel and Blade, Node.js and Express, React, Angular, jQuery, Tailwind CSS, Bootstrap, WordPress and WooCommerce, REST API design, Google Maps API, card and PayPal payments
Data: MySQL, SQLite, PostgreSQL
Cloud and DevOps: Azure (App Service, virtual machines, Key Vault), GitHub Actions, Docker and Devilbox, Linux, Apache (multi-site, reverse proxy), Let's Encrypt, SSH, Git
Monitoring and testing: Sentry, Matomo, A/B testing, manual and automated testing
Games and graphics: Godot 4, openFrameworks, Open Dynamics Engine, Dear ImGui
Hardware: Raspberry Pi, Arduino, GPIO and relays, Fusion 360 and FreeCAD
Ways of working: Leading projects, costing and planning, regular client meetings, Agile and Scrum, secure coding (prepared statements, XSS protection, password hashing), SEO and accessibility
TXT,
            'experience' => <<<'MD'
### Full-stack Developer — Designamite Limited

October 2022 – present

Development lead for [FibreNest](https://www.fibrenest.com), Persimmon Homes' fibre broadband business, in a team of six developers. Apart from approving and sending quotes, I run the FibreNest work: costing, planning, building, testing, deploying and support.

- **Persimmon Site Management App:** tracks build progress, quality inspections, material call-offs, plot issues, disputes, KPIs, weather and subcontracted work across around 400,000 plots and 20,000 users. I built the build-progress page in the old version, then designed the API spec for the new one and built its dynamic Blade front end on top of a headless back end.
- It runs on three load-balanced live instances, with separate dev, staging and training environments.
- **FibreNest website and online sign-up:** customers enter their postcode and address and get a sign-up journey tailored to where their plot is in the build.
- **FibreNest portal:** the back-office system for customer orders, fibre installations, customer support and reporting.
- **A/B testing:** worked on around 50 A/B tests for clients including Channel 4, Wickes and Dunelm.
- **Deploying and monitoring:** GitHub Actions, Docker containers, and Azure App Service and virtual machines with secrets in Azure Key Vault, plus command-line deploys where needed. Sentry for errors and Matomo for analytics.
- Meet clients regularly to agree what they need, show progress and pick up support requests.
- Wrote [DevSpeedup](https://alexdavi.es/projects/devspeedup), a desktop tool that switches Devilbox's PHP and MySQL versions on the fly. It let me support several legacy projects without hand-editing config and restarting Docker every time, which saved me hours.

### Freelance web developer — [21cs.co.uk](https://21cs.co.uk)

2026 – present, alongside Designamite

- **[The Print Shop Tunstall](https://theprintshoptunstall.co.uk):** rebuilt a printing, signage and workwear shop's website from scratch in Node.js, Express, SQLite and EJS. Customers design sticker sheets and business cards in the browser and get print-ready files, pay by card, PayPal or bank transfer, and approve proofs online.
- Built the shop an admin where the owner runs products, price lists, an orders board, proofs and email replies without needing me.
- **[Bird Box Shop](https://birdboxshop.co.uk):** designed a new WordPress block theme for a WooCommerce shop. It also fixed around 200 broken colour references across the existing pages without editing any of them.
- Set up a Docker copy of the live shop, with scripts that pull production down safely and deploy theme changes back up with a backup and rollback.
- Host both sites on my own Linux server: Apache virtual hosts, Let's Encrypt certificates and scripted deploys.

### Developer — FibreNest (Persimmon Homes)

Summer 2022

- Built a web app that shows the company's current and planned fibre-optic network on a map, so staff can see the whole network from a browser.
- Used the Google Maps API with JavaScript on the front end and PHP and PostgreSQL behind it.

### IT Support Officer — Novus Property Solutions

August 2021 – January 2022, alongside my final year

- Resolved first and second line support queries.
- Set up phones, computers and tablets for staff, following strict configuration and security standards.

### IT Systems Developer — Novus Property Solutions

July 2019 – October 2019

- Built a web app that staff use to claim back expenses like travel and accommodation, with an Angular and TypeScript front end and a Java EE and SQL back end.
- I was briefed on the requirements and given access to the systems, then left to build it, with a progress meeting every two weeks.

### Work experience — Internet Central

July 2015

Found leads for fibre broadband and visited BT exchanges with a senior engineer.
MD,
            'projects' => <<<'MD'
Full write-ups for these and more are at [alexdavi.es/projects](https://alexdavi.es/projects).

### [Pi Power Button](https://alexdavi.es/projects/pi-power-button)

A Raspberry Pi Zero W wired to my PC's power switch through a relay, with a password-protected web app that turns it on and off from anywhere. It confirms each press over the network with ARP, ping and TCP checks, and reaches my server through a locked-down reverse SSH tunnel. Python standard library only.

### [ASM Sim & Learn](https://alexdavi.es/projects/assembly-simulator)

My dissertation project, live at [alexdavi.es/asm-sim](https://alexdavi.es/asm-sim/). A 16-bit computer in the browser with a colour display, 7-segment displays and a text output, plus lessons that check your program by scanning memory and a community area for sharing and rating code. Built on AngularJS, PHP and MySQL.

### [Lil Chongee & Bud-Bro](https://alexdavi.es/projects/lil-chongee-bud-bro)

A top-down 3D comedy game in Godot 4 with two-player online co-op. Around 32,000 lines of GDScript, music and sound synthesised at runtime, and CI that builds and tests Windows, macOS and Linux releases.

### [alexdavi.es](https://alexdavi.es)

My portfolio, built in Laravel with Tailwind CSS and SQLite. It has an admin for projects and image uploads, a visitor stats dashboard, a rate-limited contact form, structured data for search engines, accessibility fixes and automated tests.

### [DevSpeedup](https://alexdavi.es/projects/devspeedup)

A C++ desktop tool built with Dear ImGui. It started as a way to switch PHP versions in a Devilbox Docker setup, and grew to cover saved commands, scripts generated from templates, and tracking lines changed and unpushed commits across Git repos.

### [Pool & Snooker Simulator](https://alexdavi.es/projects/pool-snooker-simulator)

A 3D pool and snooker game in C++ with openFrameworks and the Open Dynamics Engine. It has switchable foul rules, a stable fixed-step physics loop, and two modes of my own that hand out random rewards and punishments.
MD,
            'education' => <<<'MD'
### BSc (Hons) Computer Science — Keele University

2019 – 2022 · First Class Honours (76%)

- Dissertation: *8-Bit Assembler Learning Tool and Community*, now online as [ASM Sim & Learn](https://alexdavi.es/projects/assembly-simulator).
- Over 95% in Advanced Programming Practices, Programming II (Data Structures and Algorithms) and Mobile Application Development.
- Also studied Advanced Web Technologies, Database Systems, Games Computing, Software Engineering (Agile and Scrum), Communications and Networks, and Computational and Artificial Intelligence.

### Newcastle-under-Lyme School

2012 – 2019

- A-levels in Physics, Mathematics and Product Design.
- Nine GCSEs at A\*–C, including A\* in Mathematics, Physics and Chemistry.
MD,
            'interests' => <<<'MD'
When I'm not programming I'm usually working on my car, a Peugeot 208 GTi I've been modifying with my dad and cousins, or sim racing on a rig I keep adding to. I designed a gear shifter for it in Fusion 360. I also enjoy fishing, walking, pool and card games with friends.
MD,
        ];
    }
};
