{{-- Write-up for /projects/bird-box-shop. Renders below the project description. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>What I worked on</h2>
    <p>
        Bird Box Shop runs on stock WordPress with WooCommerce, and the owner manages products, prices and pages
        from <code>wp-admin</code> as before. My part was everything around that: a new theme, a local copy of the
        shop to work on without risking the real one, and a safe way to get changes back to production.
    </p>

    <h2>A theme that fixed the pages without editing them</h2>
    <p>
        The theme is a child of Twenty Twenty-Four. It's a block theme, so nearly all of the design lives in
        <code>theme.json</code>: the palette, type scale, spacing, and button and block styles. A small stylesheet
        covers the WooCommerce markup <code>theme.json</code> can't reach, such as product cards, the cart, the
        sticky header and focus rings. Headings are set in Cardo and everything else in Instrument Sans, both
        hosted by the theme itself, so there are no Google Fonts requests and nothing to disclose under GDPR.
    </p>
    <p>
        The interesting part was a bug I found on the way. The shop's pages had been built with an older theme,
        and about 200 colour settings saved in the database still pointed at that theme's colour names
        (<code>primary</code>, <code>secondary</code>, <code>foreground</code> and so on). Twenty Twenty-Four
        doesn't define any of them, so every one of those colours had quietly been resolving to nothing. Defining
        the old names in the new palette repaired all of them at once, without touching a single block.
    </p>
    <p>
        I also fixed the homepage's structure: it had its main heading as an <code>&lt;h3&gt;</code> and three
        category tiles as <code>&lt;h1&gt;</code>s, and the header had an empty logo block, so the shop had no name
        on any page.
    </p>

    <h2>Templates live in the database</h2>
    <p>
        The header, footer and homepage had been edited in WordPress's site editor, which means they aren't files.
        They're stored as posts, each tagged with the theme it belongs to. Switching to the new theme on its own
        would have silently dropped the shop back to Twenty Twenty-Four's stock header, footer and homepage. So
        switching re-tags those records first and repoints the homepage at them. These changes are written as PHP
        run through WP-CLI, so exactly the same code runs against my local copy and against production, rather
        than two hand-kept versions drifting apart.
    </p>

    <h2>A local copy of the live shop</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Docker:</strong> WordPress, MariaDB and WP-CLI in Docker Compose. The first start imports the live database dump.</li>
        <li><strong>Sync:</strong> one script pulls the files and database down from the server. It only ever reads from production, and it exports the database with WP-CLI on the server, so the database password never passes through the script.</li>
        <li><strong>Localise:</strong> WooCommerce saves full URLs inside its content. Without rewriting them, clicking any product on the local copy would take you to the live shop, where you could edit production by accident. A serialisation-aware search and replace fixes all of them (281 on the September 2026 copy).</li>
        <li><strong>Git:</strong> the repository holds the environment and the theme only. WordPress itself, the plugins and the uploads (about 500&nbsp;MB) and the database, which holds customer accounts, stay out of it.</li>
    </ul>

    <h2>Deploying</h2>
    <p>
        One script is allowed to write to production, and it asks before doing anything. It backs up the live
        database on the server and downloads a copy before changing anything, puts the shop into maintenance mode,
        uploads the theme, re-tags the templates and switches theme, applies the homepage fixes, takes the shop out
        of maintenance mode, and then checks that the home, shop and cart pages load. Every step is safe to run
        again, and the commands to roll back are printed at the end, or straight away if anything fails.
    </p>

    <h2>Built with</h2>
    <p>
        WordPress, WooCommerce, a block theme (<code>theme.json</code>), PHP, WP-CLI, Docker Compose, MariaDB, Bash
        and SSH.
    </p>
</article>
