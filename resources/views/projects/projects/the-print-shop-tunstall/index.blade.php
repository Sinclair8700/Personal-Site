{{-- Write-up for /projects/the-print-shop-tunstall. Renders below the project description. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>A shop that runs itself</h2>
    <p>
        The Print Shop Tunstall prints business cards, flyers, posters, stickers, signs and workwear. I replaced
        their old site with one built from scratch in Node.js and Express, with SQLite as the database. There's no
        CMS to keep patched: the shop's staff manage products, categories, the gallery of past jobs, orders,
        messages and opening hours from the site's own admin, and owners and staff get different permissions.
    </p>

    <h2>Sticker Builder</h2>
    <p>
        Customers design custom stickers in the browser, sold by the sheet. All of the image work happens on their
        device, and nothing is uploaded until they add the design to their cart.
    </p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Background removal:</strong> the background colour is detected from the edges of the image and cleared with a flood fill, or the customer can remove every near-white pixel or pick a colour, with a tolerance slider.</li>
        <li><strong>Automatic cut line:</strong> the design's outline is grown by the border width, smoothed so there are no sharp corners for the cutting blade to snag on, and traced into a path. Separate parts of a design are bridged into one piece.</li>
        <li><strong>Sheet layout:</strong> the sticker is tiled as many times as will fit on a 320&nbsp;&times;&nbsp;450&nbsp;mm sheet, keeping the 10&nbsp;mm margin the press can't print into, and turned sideways when that fits more on.</li>
        <li><strong>Editor:</strong> text in 27 typefaces, filters, a QR code generator, an actual-size preview with a bank card for scale, and a warning when the artwork would print below 150&nbsp;dpi.</li>
    </ul>
    <p>
        The shop gets three files with each order: a 300&nbsp;dpi proof, the customer's original artwork, and a
        print-ready SVG of the whole sheet at true millimetre size, with a <code>CutContour</code> line in 100%
        magenta around every sticker. That spot colour is what vinyl cutters and their software look for, so the file
        goes straight to the cutter.
    </p>

    <h2>Business Card Designer</h2>
    <p>
        The same idea for business cards: pick a layout, type your details, add a logo and see the proof. Cards are
        laid out the way the shop's own price list explains, an 85&nbsp;&times;&nbsp;55&nbsp;mm card inside a sheet
        with bleed. The designer measures the text into the safe area, wraps long lines and shrinks the whole block
        until it fits, so nothing the customer types is dropped and nothing lands where the trimmer could cut it.
        Customers can upload their own design for either side, and the logo upload reuses the sticker builder's
        background removal, which I wrote as a small library with no page or state of its own so both tools could
        share it. Each order gets a 300&nbsp;dpi PNG and a print-ready SVG of each side, plus a PDF of both.
    </p>

    <h2>Orders and money</h2>
    <p>
        The cart lives in the visitor's browser and only holds what they chose. Every price is worked out on the
        server by one function, which the cart, order and checkout pages all call, so the browser is never trusted
        about money. An order is saved before the customer leaves for Stripe or PayPal, and only becomes paid when
        the provider confirms it: by a signed webhook from Stripe, or by capturing the payment when they come back
        from PayPal. They can also pay by bank transfer, which the shop marks as received.
    </p>
    <p>
        Jobs the shop prices by hand arrive as enquiries. The shop sets a price, the site emails it, and the
        customer accepts and pays on their order page.
    </p>

    <h2>The admin side</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Orders board:</strong> open jobs in columns by stage (awaiting artwork, proof sent, proof approved, printing, ready to collect), with due dates and private shop notes. It refreshes itself and highlights new orders.</li>
        <li><strong>Proofs:</strong> a reply can be marked as the proof, which gives the customer an "approve it" link and moves the order along when they do.</li>
        <li><strong>Email replies:</strong> each order and message gets its own reply address, so when a customer answers an email, their reply lands back on the right order in the admin instead of in a shared inbox.</li>
        <li><strong>Products:</strong> price lists by quantity (which is what a real print price list looks like), questions to ask the customer, extra pictures with drag-to-reorder, and bulk edits.</li>
    </ul>

    <h2>Running it</h2>
    <p>
        Changes go to a password-protected staging copy first, which runs on a nightly copy of the live data. The
        deploy script won't put anything live unless it's on GitHub and has already been through staging. Each
        deploy backs up the database first and shows a maintenance page for the second or two the site is down.
        The server side is systemd, Apache and Let's Encrypt, all set up from scripts in the repository.
    </p>

    <h2>Built with</h2>
    <p>
        Node.js, Express, SQLite (<code>better-sqlite3</code>), EJS, vanilla JavaScript, sharp, Nodemailer, Stripe,
        PayPal, Postfix, Apache and systemd.
    </p>
</article>
