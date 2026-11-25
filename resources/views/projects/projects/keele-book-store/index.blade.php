{{-- Write-up for /projects/keele-book-store. Renders below the project description.
     Screenshots come from the coursework report; the card image is main.png next to this file. --}}
@php
    $fig = function (string $name, int $w, int $h, string $alt, string $caption, string $max = '') {
        $src = asset('images/projects/keele-book-store/'.$name.'.webp');
        return '<figure class="my-2 flex flex-col gap-2 w-full '.$max.'">'
            .'<img src="'.$src.'" width="'.$w.'" height="'.$h.'" alt="'.e($alt).'" loading="lazy" decoding="async" class="w-full h-auto rounded-lg">'
            .'<figcaption class="text-sm text-white/60">'.e($caption).'</figcaption></figure>';
    };
@endphp
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>The brief</h2>
    <p>
        This was my coursework for the Advanced Web Technologies module at Keele University in spring 2022: build
        an online shop for a bookstore company, with registration and login, validation done in React, protection
        against common attacks, search engine optimisation, and a RESTful web service the company's manager uses
        to handle orders. It has three pages: the store, a combined login and registration page, and the
        manager's orders page.
    </p>

    <h2>Browsing and buying</h2>
    <p>
        Two dropdowns pick a category and a type of book (for example School Books, then English), and the books
        load in the background with AJAX. The grid shows four books a row on large screens, then three, two, and
        one on a phone. Anyone can browse. Logging in reveals the cart and an <em>Add To Cart</em> button on each
        book. Each click adds another copy and shows a tick for a moment, and the cart button keeps a running
        total.
    </p>
    {!! $fig('store-logged-out', 1918, 955, 'The book store page with category and type dropdowns and a grid of English textbooks with prices', 'The store, before logging in.') !!}
    <div class="flex flex-col sm:flex-row gap-4 items-start">
        {!! $fig('cart', 508, 409, 'Cart dialog listing four English textbooks with Remove and Add Another buttons, a total of 69.96 pounds and a Place Order button', 'The cart: change quantities, then place the order.', 'sm:w-3/5') !!}
        {!! $fig('mobile', 388, 684, 'The store on a phone, with the books in a single column', 'On a phone the grid drops to one column.', 'max-w-[220px] sm:w-2/5') !!}
    </div>
    <p>
        The cart opens in a Bootstrap modal where each book can be removed or added again. <em>Place Order</em>
        sends the order, shows "Order Placed" for a couple of seconds, then clears the cart.
    </p>

    <h2>Accounts and validation</h2>
    <p>
        Customers register with a phone number, password, email and name, and log in with their phone number and
        password. Every field is checked as you type in React and checked again on the server:
    </p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li>Phone numbers must be exactly 10 digits.</li>
        <li>Passwords must be 6 to 31 characters, and the repeated password has to match.</li>
        <li>Names and emails reject disallowed characters, and the message names the character you typed.</li>
        <li>The login and register buttons only switch on once every field is valid.</li>
        <li>An email that is already registered is refused, with a message saying so.</li>
    </ul>
    <p>
        Writing an email regex that never rejected a real address turned out to be harder than expected, so the
        final check for emails happens in PHP on the server.
    </p>
    {!! $fig('register-validation', 413, 229, 'Register form with phone number, password, repeat password, email and name fields, each showing a valid status, and the message Registration Successful Please Login', 'Registration with every field passing validation.', 'max-w-sm') !!}

    <h2>The manager's orders page</h2>
    <p>
        Users marked as managers get an <em>Orders</em> button that opens the order service. It lists every order
        with the customer's details, the books and the total. It refreshes every five seconds, and a button
        pauses and resumes the updates. A dropdown filters by status (New, Payment Received, Sent, Cancelled or
        No Answer). Orders are listed newest first, except when filtering to New, which lists the oldest first so
        the longest-waiting ones are at the top. Each order's status can be changed in place.
    </p>

    <h2>Security and data</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li>Every database query uses prepared statements, which prevents SQL injection.</li>
        <li>
            Everything a customer types goes through <code>htmlspecialchars</code> before it is shown, so nothing a
            customer enters can run as a script on the manager's page.
        </li>
        <li>
            The data lives in MySQL tables linked with foreign keys: users, books, categories, types, orders, and an
            <code>order_to_book_relation</code> table, because an order can hold many books and a book can be in many
            orders.
        </li>
        <li>For SEO, the store has meta descriptions and keywords, a <code>sitemap.xml</code>, alt text on every image and proper headings.</li>
    </ul>

    <h2>Problems I solved</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li>
            The category and type dropdowns sometimes requested books before the data they needed had arrived,
            because React's <code>setState</code> is asynchronous. Moving the follow-up requests into
            <code>setState</code>'s callback fixed it.
        </li>
        <li>
            Adding each order's books into the order's JSON gave an array that logged fine in the console but
            couldn't be looped over. Loading the books inside the order component itself fixed it.
        </li>
    </ul>

    <h2>What I'd add next</h2>
    <p>
        Pagination for both the store and the orders page, a front-end email check that matches the server's,
        and shared PHP functions for repeated work instead of copies in each endpoint.
    </p>

    <h2>Built with</h2>
    <p>
        React, jQuery and Bootstrap on the front end. PHP and MySQL (managed in phpMyAdmin) on the server. About
        1,400 lines of my own JavaScript and 14 PHP endpoints. All book images are CC0.
    </p>
</article>
