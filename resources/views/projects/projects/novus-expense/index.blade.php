{{-- Write-up for /projects/novus-expense. Renders below the project description.
     The card image (main.png next to this file) is the travel booking form, with test data. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>The brief</h2>
    <p>
        I built Novus Expense from July to October 2019, as an IT Systems Developer at Novus Property Solutions, just
        after finishing sixth form. It's the web app staff use to claim back expenses like travel and accommodation,
        and to book travel for work. I was briefed on the requirements, given access to the systems it needed, and
        then left to build it, with a progress meeting every two weeks. It was adopted across the whole company.
    </p>

    <h2>What it does</h2>
    <p>The menu down the left follows a claim from the person who makes it to the people who sign it off:</p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Claims:</strong> staff create expense claims and mileage claims, and track their own under My Claims.</li>
        <li><strong>Authorisation:</strong> approvers search for claims and authorise them.</li>
        <li><strong>Finance Review:</strong> the finance team searches for claims and reviews them.</li>
        <li>
            <strong>Travel:</strong> a booking form for accommodation, trains, ferries and flights. A booking carries a
            cost code and an office, and can be made on behalf of another employee by entering their username.
            Accommodation requests include the guests (one person or a group), how close to the location it needs to be,
            the dates, the number and type of rooms, and whether breakfast and an evening meal are wanted.
        </li>
        <li><strong>Reports</strong> and <strong>User Accounts</strong> for the people who run it.</li>
    </ul>

    <h2>How it's built</h2>
    <p>
        The front end is Angular, written in TypeScript, and talks to a Java EE back end that stores everything in an
        SQL database.
    </p>
    <p class="text-white/60 text-sm">
        The screenshot is from testing, so the names, cost code and dates in it are made up.
    </p>
</article>
