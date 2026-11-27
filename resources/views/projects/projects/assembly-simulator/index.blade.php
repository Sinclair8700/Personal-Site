{{-- Write-up for /projects/assembly-simulator. Renders below the project description.
     The dissertation PDF is public/documents/asm-sim-and-learn-dissertation.pdf and its
     figures are public/images/projects/assembly-simulator/*.webp. --}}
@php
    $pdf = asset('documents/asm-sim-and-learn-dissertation.pdf');
    $fig = function (string $name, int $w, int $h, string $alt, string $caption, string $max = '') {
        $src = asset('images/projects/assembly-simulator/'.$name.'.webp');
        return '<figure class="my-2 flex flex-col gap-2 w-full '.$max.'">'
            .'<a href="'.$src.'" target="_blank" rel="noopener" class="!no-underline">'
            .'<img src="'.$src.'" width="'.$w.'" height="'.$h.'" alt="'.e($alt).'" loading="lazy" decoding="async" class="w-full h-auto rounded-lg">'
            .'</a><figcaption class="text-sm text-white/60">'.e($caption).'</figcaption></figure>';
    };
    $thesisLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Thesis',
        'name' => '8-Bit Assembler Learning Tool and Community',
        'author' => ['@type' => 'Person', 'name' => 'Alex Davies'],
        'datePublished' => '2022-04',
        'inSupportOf' => 'BSc Computer Science',
        'sourceOrganization' => ['@type' => 'CollegeOrUniversity', 'name' => 'Keele University'],
        'url' => url('/projects/assembly-simulator').'#dissertation',
        'encoding' => ['@type' => 'MediaObject', 'contentUrl' => $pdf, 'encodingFormat' => 'application/pdf'],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($thesisLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7 break-words
    [&_h2]:mt-6 [&_h3]:mt-6 [&_h3]:scroll-mt-24 [&_h4]:mt-3 [&_h4]:font-semibold
    [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>About the project</h2>
    <p>
        ASM Sim &amp; Learn was my final-year project for my BSc in Computer Science at Keele University
        (module CSC-30014, 2021&ndash;22). It is a small computer that runs in the browser, with lessons that teach
        assembly language one step at a time and a community area where people post, rate and run each other's
        programs.
    </p>
    <p>
        The computer is built on Marco Schweighauser's open-source
        <a href="https://github.com/Schweigi/assembler-simulator" target="_blank" rel="noopener">assembler-simulator</a>
        (AngularJS). I widened its memory from 8-bit to 16-bit addressing and added three outputs: a 20&times;20
        display with 8-bit colour, three 7-segment displays and a 24-character text output. Around it I built
        accounts, lessons with automatic checking, and the community area, in PHP and MySQL with jQuery and
        Bootstrap on the front end. Ten people tested it during development.
    </p>

    <h2>Back online</h2>
    <p>
        In November 2026 I brought it back to life at
        <a href="https://alex-davies.com/asm-sim/" target="_blank" rel="noopener">alex-davies.com/asm-sim</a>. It now runs
        on PHP&nbsp;8.4. PHP 8.1 and later turn database errors into exceptions, so I switched that off to let
        messages like "username taken" reach the page again. It reads its database login from a config file
        outside the web root, and it refuses lesson names containing slashes or <code>..</code>, because those
        names become file paths. The community area also has ten new example programs that show off each output:
    </p>
    {!! $fig('live-liftoff', 800, 500, 'The live simulator running the Liftoff example: assembly code on the left, T-MINUS on the text output, a rocket on the 20 by 20 display and the countdown on the seven-segment displays', 'The live site running "Liftoff!", which uses all three outputs at once.') !!}
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Text output:</strong> Fibonacci Sequence, Prime Numbers (trial division with nested loops), and a Scrolling Marquee that wraps a long message through the 24 characters.</li>
        <li><strong>20&times;20 display:</strong> Rainbow Stripes, an XOR Pattern built with AND, SHL, SHR and OR, a Smiley Face drawn from a 16&times;16 bitmap stored one 16-bit row per number, and a Bouncing Ball that leaves a colour-changing trail.</li>
        <li><strong>7-segment displays:</strong> a 000&ndash;999 Counter and a HELLO Scroller made from segment letters.</li>
        <li><strong>All three:</strong> Liftoff!, a countdown that ends with a rocket flying off the top of the display.</li>
    </ul>
    <p>
        <x-button type="a" href="{{ $pdf }}" target="_blank" rel="noopener" class="inline-block !no-underline">
            Read the dissertation (PDF, 2.8&nbsp;MB)
        </x-button>
    </p>

    <h2 id="dissertation">The dissertation</h2>
    <p class="text-white/70">
        Below is the full text of <em>8-Bit Assembler Learning Tool and Community</em>, the dissertation I
        submitted to Keele University's School of Computing and Mathematics in April 2022 in partial fulfilment of
        the requirements for the degree of Bachelor of Science. It is the text as submitted, with a selection of
        its figures. Every figure, all twelve interface sketches and the appendices (the project plan, ethics
        forms and survey forms) are in the <a href="{{ $pdf }}" target="_blank" rel="noopener">PDF</a>.
    </p>

    <nav aria-label="Dissertation contents" class="border-2 border-white/30 rounded-lg px-4 py-3 bg-black">
        <p class="font-bold">Contents</p>
        <ol class="list-decimal pl-6 text-sm leading-6 mt-1">
            <li><a href="#abstract">Abstract</a></li>
            <li><a href="#acknowledgements">Acknowledgements</a></li>
            <li><a href="#introduction">Introduction</a></li>
            <li><a href="#previous-work">Overview of previous work</a></li>
            <li><a href="#aims">Aims &amp; objectives</a></li>
            <li><a href="#plan">The plan and data model</a></li>
            <li><a href="#prototype-1">Prototype 1 &ndash; Interface design</a></li>
            <li><a href="#prototype-2">Prototype 2 &ndash; User login</a></li>
            <li><a href="#prototype-3">Prototype 3 &ndash; Community posts</a></li>
            <li><a href="#prototype-4">Prototype 4 &ndash; User interface and computer section</a></li>
            <li><a href="#prototype-5">Prototype 5 &ndash; Creation of lessons</a></li>
            <li><a href="#testing">Testing &amp; results</a></li>
            <li><a href="#evaluation">Evaluation</a></li>
            <li><a href="#conclusion">Conclusion</a></li>
            <li><a href="#references">References</a></li>
            <li><a href="#poster">Project poster</a></li>
        </ol>
    </nav>

    <h3 id="abstract">Abstract</h3>
    <p>
        This report looks into the development of a web-based learning tool that uses visual learning techniques
        to educate those who are curious about the field of assembly programming on the basics of the subject so
        that they may learn how to manoeuvre around from more specific assembly architectures in fields such as
        reverse engineering and programming embedded programming on microcontrollers. Current tools available are
        not very interactive and can leave the user wanting. To make the learning tool fit for its purpose user
        interaction testing has been performed to iron out any bugs and make the site operate smoothly and securely
        with components useful to the learner. This project concludes that the missing piece in current forms of
        assembly education is user-to-user interactions. The outcome of this project can be seen in full at
        <a href="https://alex-davies.com/asm-sim/" target="_blank" rel="noopener">Learn Assembler (alex-davies.com)</a>.
        To use some of the functionality like completing lessons and viewing code submissions, registration and
        login are required.
    </p>

    <h3 id="acknowledgements">Acknowledgements</h3>
    <p>
        I would like to thank my family, friends, and the wonderful staff at Keele University for their support in
        the completion of my project. Thank you to Dr. Adam Stanton for sponsoring my project and pointing me in
        the direction of creating a learning tool to sit on top of a prebuilt 8-bit assembler computer library
        rather than &ldquo;reinventing the wheel&rdquo;. I would also like to thank him for the inspiration he
        provided with further reading and examples in the field of teaching programming interactively and
        entertainingly. I also thank Dr. Theocharis Kyriacou for picking up my project in the absence of Dr. Adam
        Stanton and seeing it through to completion. Thanks to my family and friends for agreeing to be test
        subjects for my project, whether it be for the user interface design or for assessing the quality of the
        teaching methods employed by my project.
    </p>

    <h3 id="introduction">Introduction</h3>
    <p>
        The problem that will be investigated within this dissertation is the lack of learning tools available to
        the general public which can teach them the basics of the fundamentals of computer science and the
        assembly language. This matters academically because when approaching the field of computer science, as a
        layman it's normal to feel out of your depths and not have any idea where to get to grips with the subject
        without guidance. That's where the product of this project will come in handy. The idea for this project
        is to create a place to start for budding computer scientists to get a foothold in the field.
    </p>
    <p>
        What is assembly language? We can look to dictionaries to find the skin-deep answer. Britannica defines it
        best as an &ldquo;assembly language, type of low-level computer programming language consisting mostly of
        symbolic equivalents of a particular computer's machine language. Computers produced by different
        manufacturers have different machine languages and require different assemblers and assembly languages.
        Some assembly languages can be used to convert the code that programmers write (source code) into machine
        language (readable by the computer) and have functions to facilitate programming (e.g., by combining a
        sequence of several instructions into one entity). Programming in assembly languages requires extensive
        knowledge of computer architecture.&rdquo; Other dictionaries give similar definitions, but none of them go
        into the details of the instructions and logic which &ldquo;requires extensive knowledge of computer
        architecture.&rdquo;, It uses number bases that are outside of every day denary system&hellip; base 16 or
        Hexadecimal and base 2 or Binary, these must be understood to interoperate things like addresses to blocks
        of memory and bits within those blocks. Terms like PUSH, POP, and JMP are used along with 3680 other
        mnemonic and operand type instruction tags if you look at the common x86 assembler specification (How Many
        x86-64 Instructions Are There Anyway?, 2022).
    </p>
    <p>
        Knowing how to navigate assembler language, even if the instruction sets are different, is important to
        understand in several fields. If you wished to go into, or study the field of reverse engineering compiled
        software, knowing how similarly named instructions work together in the program would be important to
        figure out the workings of well-crafted computer viruses or perhaps to achieve interoperability with other
        systems that have been independently created as reading through code that has been translated down an
        order of abstraction by a compiler is often required. (Ethics in Computing - Reverse Engineering, 2022) The
        field of reverse engineering itself is used as a learning tool for other concepts in Computer Science so
        the product of this project may be used as a steppingstone into largely unexplored areas of computer
        science to the user. It will also be needed if embedded programming is being pursued on small
        microcontrollers or in developing low-level code such as drivers (Hyde, 2010).
    </p>

    <h3 id="previous-work">Overview of Previous Work</h3>
    <p>When one input the basic search term &ldquo;learn assembly&rdquo; into a search engine such as google the first results presented are as follows:</p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><a href="https://www.tutorialspoint.com/assembly_programming/index.htm" target="_blank" rel="noopener">https://www.tutorialspoint.com/assembly_programming/index.htm</a></li>
        <li><a href="https://www.section.io/engineering-education/assembly-part-1/" target="_blank" rel="noopener">https://www.section.io/engineering-education/assembly-part-1/</a></li>
        <li>Art of Assembly Language, 2nd Edition</li>
    </ul>
    <p>
        These sites may be excellent learning tools for a user who can learn easily just through reading, however,
        they lack any user interaction. For some this makes learning an uphill battle from the get-go, this can be
        because learning a new skill requires motivation as motivation is an influential factor in
        teaching-learning situations (Filgona, Sakiyo, Gwany, and Okoronka, 2020). Motivation is the driving force
        behind a student reaching their education goals and the job of a teacher to promote (in this case the
        learning functionalities of the website). When looking at levels of motivation from someone's needs being
        met, one can have five distinct categories immerge according to Maslow's Hierarchy of Needs (McLeod, S. A.
        2018, May 21). Each higher layer in the hierarchy can only be achieved if the needs below it are met. In
        this case, the first two levels can be ignored as these needs are basic qualifiers for any form of
        education. They are Safety needs (physical and mental security) and Physiological needs (food, water,
        warmth, and rest). Without these two needs being met needs above them cannot be properly met.
    </p>
    <p>
        With existing methods available to the user for learning assembly language outside of formal education
        (e.g. A Computer Science course at university) it can be seen that the next rung in the ladder of
        motivation, is missing, the need for belongingness. When using them to learn it is just you the user and
        the text on the screen. You as the user are left to your own devices to try this assembly code and try to
        digest the information laid out in slabs to crawl through page by page and note your progression as you go
        manually. The content however is of high quality and the lessons of this site will make use of the
        techniques used in them.
    </p>
    <p>
        The site will require an assembly language simulator for users' creations to run on. Rather than reinvent
        the wheel so to speak by creating an assembler simulator, premade libraries for running a computer in the
        browser.
    </p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><a href="https://github.com/taniarascia/chip8" target="_blank" rel="noopener">https://github.com/taniarascia/chip8</a></li>
        <li><a href="https://github.com/Schweigi/assembler-simulator" target="_blank" rel="noopener">https://github.com/Schweigi/assembler-simulator</a></li>
    </ul>
    <p>
        Tania Rascia's Chip-8 emulator runs CHIP-8 applications which are written similarly to assembler just with
        a different naming convention for the operations. This would work perfectly with some modifications,
        however, the example programs provided in the demo do not run, lowering confidence in its viability as a
        base for the site. Marco Schweighauser's assembler-simulator however, worked out of the box so it could be
        easily adapted to the project knowing that any assembly code put into it would produce a proper output and
        errors would be shown accordingly. The instructions that the computer accepts are also very well
        documented by the author with examples of their syntax. It shows a text output by default which is mapped
        to the end of the memory region of the computer. The memory of the computer is shown in real-time with
        AngularJS used to update the page. Addresses stored in registers are highlighted in the memory to help
        visualise what's going on under the hood of the computer.
    </p>
    <p>
        The paper &ldquo;Visual Learning for Science and Engineering&rdquo; published by IEEE covers how visual
        learning, which is the use of graphics, images, and animations, is a strategy that educators are using
        increasingly to effectively teach sciences and engineering. It discusses how important visual learning
        methods are for exploiting students' visual senses to heighten the efficiency of information uptake and
        increase the retention rate of students in STEM fields. The benefits of visual learning are exposed to new
        ways to solve problems, it provides an alternative way of thinking about the task. It has the potential to
        enhance the education and practice of science and engineering.
    </p>

    <h3 id="aims">Aims &amp; Objectives</h3>
    <p>A list of the broad aim and objectives of this project. The project aims to:</p>
    <ul class="list-disc pl-6">
        <li>
            Help people learn the fundamental workings of a computer and how the assembly language can be used to
            manipulate the processor to produce the desired output for the user. With a lesson system that notes
            user progress and a community area that can allow users to read through others' code, run it
            themselves, and make their own tweaks.
        </li>
    </ul>
    <p>The objectives of the project are:</p>
    <ol class="list-decimal pl-6 flex flex-col gap-2 [&_ol]:pl-6 [&_ol]:mt-1 [&_ol]:flex [&_ol]:flex-col [&_ol]:gap-1">
        <li>
            Produce a functional and user-friendly web interface for the following components:
            <ol class="list-[lower-alpha]">
                <li>Assembler Computer</li>
                <li>Lesson selection page</li>
                <li>Lesson creation page</li>
                <li>Community submission area</li>
                <li>User account page</li>
            </ol>
        </li>
        <li>Have easily selectable assembly instructions that show a description once clicked and are copied to the user's clipboard for use in the code section of the computer.</li>
        <li>
            Have the following outputs that the user can output to with the assembly that they write within the
            code section of the computer section:
            <ol class="list-[lower-alpha]">
                <li>2D drawing plane with 8-bit colour (first 2 bits for red, next 3 bits for green, and the next 3 bits for blue).</li>
                <li>Three 7-segment displays that can have their segments individually controlled by bit values in memory with 1 bit for each segment in a byte.</li>
                <li>A simple 24-character text output that can be written to with ASCII characters.</li>
            </ol>
        </li>
        <li>Have some sort of label that will appear when an output is clicked that displays some information about its workings and potentially a demo.</li>
        <li>
            Create a login system for user-created accounts
            <ol class="list-[lower-alpha]">
                <li>Process logins.</li>
                <li>Process registrations.</li>
                <li>Process account deletions.</li>
                <li>Provide a user area for users to see their community posts, completed lessons, and account options</li>
                <li>
                    Have permission levels (admin, normal user).
                    <ol class="list-[lower-roman]">
                        <li>Normal users can delete their accounts and own posts with the option to be forgotten to comply with GDPR.</li>
                        <li>Normal users can create and delete their community posts.</li>
                        <li>Normal users can upvote and downvote posts.</li>
                        <li>Admins can do everything a normal user can but also can delete other's community posts, users, and lessons.</li>
                    </ol>
                </li>
            </ol>
        </li>
        <li>
            Provide an area for user-submitted 8-bit assembly code submissions
            <ol class="list-[lower-alpha]">
                <li>Process submissions of code snippets from the computer section of the site.</li>
                <li>Create a community area for searching for code submissions by other users.</li>
                <li>Allow users to upvote and downvote submissions by other users.</li>
                <li>Allow users to sort community submissions by submission date (new to old and old to new) and rating (highest first, lowest first)</li>
                <li>Allow users to click submissions and run them on the computer</li>
            </ol>
        </li>
        <li>
            Provide an area for lessons in fundamental computer science concepts and assembly language
            <ol class="list-[lower-alpha]">
                <li>
                    Allow admins to upload lessons that meet the required format
                    <ol class="list-[lower-roman]">
                        <li>Lesson title</li>
                        <li>Lesson type (Instructions, Core Computer Science Concepts, Using the Outputs, etc.)</li>
                        <li>Lesson description</li>
                        <li>Lesson content
                            <ol class="list-decimal">
                                <li>Lesson steps</li>
                                <li>Lesson concepts</li>
                            </ol>
                        </li>
                        <li>Lesson hints (if any are needed)</li>
                        <li>Lesson expected outputs (if any are needed)</li>
                    </ol>
                </li>
                <li>Allow users to upvote and downvote lessons so any inadequate or incorrect lessons can be noticed and fixed</li>
                <li>Make lessons within their lesson types, incrementally more difficult as previous concepts are built upon and revised.</li>
            </ol>
        </li>
        <li>
            Validate all user input forms of data between the host and server
            <ol class="list-[lower-alpha]">
                <li>Host validation &ndash; Javascript</li>
            </ol>
        </li>
    </ol>

    <h3 id="plan">Main Report Body: The Plan</h3>
    <p>
        To start a project spanning a year's study, the first thing that is needed is a sound plan and the bedrock
        of a plan is its methodology which the rest can be built upon. The methodology selected for the plan was
        Evolutionary Prototyping. With the project being a tool used for learning, or just showing off your
        creations to other users, it requires user testing and feedback throughout its conception, development,
        and testing phases to figure out what works best for the majority if not all users. User testing will help
        determine if the project is heading in the right direction and allow you to make any changes to your plan
        or project as needed. It is also important to aim to get prototypes finished for the core functionality of
        the project first so that later prototyping should just focus on minor improvements, meaning if time does
        get cut short due to going off-plan, there is still an unpolished but functioning product that can be
        displayed.
    </p>
    <p>
        Another methodology that is prevalent in software engineering projects is waterfall-style development, it
        does not have separate sprint-like development cycles with user testing between them, but instead has one
        development stage that is tested at the end of development and should work to the specification so that
        it can be deployed. Waterfall development is far too rigid for this type of project, so the more fitting
        evolutionary prototyping has been used. To make the plan each of the main objectives of the plan was split
        up into prototypes.
    </p>
    {!! $fig('project-plan-gantt', 1600, 371, 'Gantt chart splitting the year into five prototypes, each followed by user testing, then in-depth user testing, the final product and documentation', 'The project plan: each prototype is followed by a round of user testing.') !!}
    <p>
        Each of the prototypes had some amount of user testing in small groups but during &ldquo;in-depth user
        testing&rdquo;, a total of 10 users tested the site.
    </p>

    <h4>Designing the conceptual and logical model</h4>
    <p>
        The entities that emerge from the objectives of the project are Users of the site, Lessons, and Code
        Submissions. User login only requires a name, password to keep others from using your account, creation
        date to show how long a user has been on the site, a type to differentiate the users from the admins, and a
        current lesson to return to the previously completed lesson so the user can review what they covered last
        time. In the model Users can complete lessons, submit code. The conceptual model is as such:
    </p>
    {!! $fig('conceptual-model', 1600, 1521, 'Conceptual model diagram with Lesson, User, CodeSubmission and Admin entities and their attributes and operations', 'Conceptual model.', 'max-w-xl') !!}
    <p>
        Translating the conceptual model into a logical model that could be used on the database management
        platform phpMyAdmin, created some many-to-many relationships with ratings and lesson completion status so
        new entities had to be created as an intermediate with foreign keys to the original linked entities.
    </p>
    {!! $fig('logical-model', 1600, 915, 'Logical model diagram showing the user, lesson and code submission tables linked through rating and lesson completion tables', 'Logical model.') !!}
    <p>
        Having these models made before development makes future work in SQL queries and PHP pages simpler to
        complete since the relations and attributes are readily available.
    </p>

    <h4>Selection of technologies</h4>
    <p>
        To shorten development time libraries were used. JQuery was selected because of its &ldquo;write less, do
        more&rdquo; style. It allows a developer to select HTML DOM elements concisely and can handle AJAX requests.
        To make the site reactive and responsive bootstrap was chosen. It comes with a plethora of HTML, js, and CSS
        objects that save the developer from having to make them manually. Bootstraps classes let a developer set
        sizes that will change the layout depending on the size of the display. Some bootstrap scss is used to
        apply custom theming to the site.
    </p>

    <h4>Picking the colour scheme</h4>
    <p>
        When designing a site Google chrome offers a tool called element selection. It was used in this project to
        display the contrast values of the site. It warns when an element has too little contrast that will make it
        hard to read. With the help of this tool, this dark, low contrast colour scheme was transformed into the one
        seen in Prototype 5.
    </p>
    {!! $fig('early-colour-scheme', 1600, 801, 'Early version of the computer page in a dark, low-contrast grey and green colour scheme', 'The early, low-contrast colour scheme.') !!}

    <h4>Setting up the webserver</h4>
    <p>
        A web hosting service was purchased from a reliable provider that came with Apache and phpMyAdmin
        preloaded, this made the creation of the tables and the website rapid as going through install processes
        was not needed. The site also came with an SSL certificate so HTTPS end-to-end encryption is being used for
        POST data by default with a rewrite of HTTP to HTTPS in the .htaccess file.
    </p>

    <h3 id="prototype-1">Prototype 1</h3>
    <p>
        The initial mockups for the prototype were done on the computer page. Three designs were created for both
        computer and mobile with the main components from the assembly computer in mind; dedicating space to them
        accordingly. When shown to users, Computer desktop prototype 1.2 and Computer mobile prototype 1.1 were
        selected as the most favourable designs (shown in the Results section).
    </p>
    <div class="flex flex-col sm:flex-row gap-4 items-start">
        {!! $fig('sketch-desktop-1-2', 1600, 1133, 'Hand-drawn desktop layout with page buttons along the top, a large code box, outputs and registers and memory panels, and run, step and reset buttons', 'Computer desktop prototype 1.2, the chosen desktop layout.', 'sm:w-3/5') !!}
        {!! $fig('sketch-mobile-1-1', 1600, 2340, 'Hand-drawn mobile layout with page buttons, a code box, run, stop and reset buttons and an outputs section stacked vertically', 'Computer mobile prototype 1.1, the chosen mobile layout.', 'max-w-[220px] sm:w-2/5') !!}
    </div>
    <p>
        As prototype 1.2 set the pattern of having the pagination at the top rather than the side of the page all
        future prototypes will follow that pattern. The next UI design that was prototyped was the community and
        lesson selection pages.
    </p>
    <div class="flex flex-col sm:flex-row gap-4 items-start">
        {!! $fig('sketch-community-desktop-1-1', 1600, 1139, 'Hand-drawn community and lesson selection page for desktop with a search bar, number per page and sort options above a list of submissions', 'Community and lesson page desktop prototype 1.1.', 'sm:w-3/5') !!}
        {!! $fig('sketch-community-mobile-1-1', 1600, 2315, 'Hand-drawn community and lesson selection page for mobile with search, sort and a list of submissions with previous and next buttons', 'Community and lesson page mobile prototype 1.1.', 'max-w-[220px] sm:w-2/5') !!}
    </div>
    <p>
        Of the three community &amp; lesson selection prototypes created, prototype 1.1 of both mobile and desktop
        were chosen when given to users for feedback. The next step in the prototyping of pages was the create a
        digital copy of the most favoured design which showcases some of the intended functionality of the site.
        The testers were asked to ignore the colour scheme as it would be changed later.
    </p>
    {!! $fig('computer-prototype-1-4', 1600, 911, 'Digital mock-up of the computer page with instruction buttons, a code box, outputs, and CPU and memory panels', 'Computer prototype 1.4.') !!}
    <p>
        This more detailed view of what the page will look like layout and content-wise on the screen was the
        anchor of the styling so other prototypes for mobile and individual components followed its layout. It
        appeared in my project poster to showcase the project before the UI was finalised.
    </p>

    <h3 id="prototype-2">Prototype 2 &ndash; User Login</h3>
    <p>
        Prototype 2's goal was to create the login system between the Javascript of the site and PHP and SQL on the
        server. The functionality of this prototype was tested by both the project's author and 3 users.
    </p>
    <h4>Login</h4>
    <p>
        On the server-side, the request is checked for both username and password fields, if they are present then
        a select query is made to the database which checks for the unique username and returns the row if
        applicable. The password provided is then hashed and compared to the stored hashed password. If the
        username or password do not match, then the user is told that the login failed. Otherwise, it is a
        successful login, and session variables for the login, users id, users type id, and username are set.
    </p>
    <h4>Register</h4>
    <p>
        The registration section can be opened from within the login section by clicking register. If the values
        for the username and password inputs pass validation they will be sent to the server, if the user with the
        name chosen does not exist then an account is made.
    </p>
    {!! $fig('register-form', 1600, 1421, 'Register dialog with username, password and repeat password fields and Close and Register buttons', 'The register form.', 'max-w-sm') !!}
    <h4>Logout</h4>
    <p>
        Logging out is a very simple task. Upon the button being clicked on the site, Javascript sends a get request
        to the server, which starts the PHP session, then deletes all its session data, causing the user login to
        not be recognised.
    </p>
    <h4>Delete Account</h4>
    <p>
        The delete account button is shown on the account page alongside the logout button. The button shown in the
        figure below is one of 2 that can appear on the site. For all users their account can be deleted from the
        account page but if a user is an admin and browsing the submissions page the option to delete accounts
        shows alongside the option to delete the post.
    </p>
    <h4>Security</h4>
    <p>
        Passwords for users are stored in hashed format this is to prevent the leaking of sensitive user data if the
        database falls victim to an attack. When an account is deleted by a user or an admin, due to foreign keys to
        its userID, the code submission, lesson completion, and voting data is cleared in line with a user's right
        to be forgotten under GDPR.
    </p>
    <h4>Validation</h4>
    <p>
        Usernames and passwords are checked for length on both the host and server. Upon registration usernames are
        validated against the criteria of length with 5 characters being the shortest it can be and 32 being the
        longest, for passwords the minimum length is raised to 6 to give slightly stronger passwords. In the
        registration, process usernames are stripped of any HTML tags to prevent a potential XSS attack when the
        username is displayed in the code submissions section. On the registration form, when the repeat password
        box loses focus JS in the background checks for password matching and if they do not match a warning
        appears.
    </p>

    <h3 id="prototype-3">Prototype 3 &ndash; Community Posts</h3>
    <h4>Creation</h4>
    <p>
        On the computer page of the site, the post button is made available to the user if they are not currently
        doing a lesson or testing a code submission. They may choose a name that is between 5 and 120 characters
        long for their submission and a description that is between 5 and 360 characters long. Code for the
        submission will be taken from the source code box on the webpage and added to the form data when it is
        sent to the server. When code submissions are created, they are given a random 32 character string as a
        file path, this is done to allow for multiple same-named submissions to point to different files.
    </p>
    <h4>Ratings</h4>
    <p>
        The logic behind ratings is set up to have a total rating for a post and have the individual user and
        submission rating pair logged. It is set up this way for multiple reasons. One, if the user deletes their
        account then their vote record can be deleted but the total rating doesn't change. Two, when displaying code
        submissions it would be computationally expensive to keep counting up the individual vote records so the
        total is kept for quick access. Three it makes it possible to track if a user has rated before, if they have
        upvoted and they click upvote, remove the vote, but if they previously downvoted and clicked upvote change
        the vote to an upvote.
    </p>
    <h4>Getting the code submissions and their data</h4>
    <p>
        On the community page, there is a search bar that can search for the name and username of the poster
        individually or in the same request. Search terms will return results that the term is a substring of the
        name, so only partial matching is needed for a successful hit. The sorting methods Newest, Oldest, Highest
        Rating, Lowest Rating can be used to order the submissions returned. The number of submissions to show on
        each page can also be selected (5-40 capped).
    </p>
    <h4>Showing code submissions</h4>
    <p>
        When showing code submissions, the data shown to the user is fundamentally the same no matter what type the
        user is. The title, description, submitted by, rating and date are shown as cards on the page. There are
        checks in place for if there are previous and next pages before showing the next and back page buttons so
        that the user doesn't get misled as to where they are page-wise. The difference between an Admins view of
        the submissions page and the standard users view is that standard users only get shown the delete button
        for their posts and admins are shown delete buttons for all posts and delete buttons for user accounts.
    </p>
    {!! $fig('submissions-admin-view', 1600, 581, 'Community page in the admin view: a search bar and two code submission cards, each with Delete Account and Delete Code Submission buttons', 'The admin view of the community page.') !!}
    <h4>Using code submissions</h4>
    <p>
        For a user to run a code submission all they have to do is click inside the code submissions card and they
        will be taken to the computer where the submission will be loaded.
    </p>
    <h4>Security</h4>
    <p>
        All-access to user posts requires a login, so a web scraper won't be able to pick up user names and
        submissions from the site. All fields are stripped of HTML entities in both PHP and javascript to stop XSS
        attacks. The PHP code only allows admins to delete posts from users other than themselves and delete others'
        accounts.
    </p>
    <h4>Validation</h4>
    <p>If the Name or Description is too long or short the user is notified before they can submit it.</p>

    <h3 id="prototype-4">Prototype 4 &ndash; Implementation of User Interface and Computer Section</h3>
    <h4>Instruction buttons and popovers</h4>
    <p>
        For each instruction available to the computer there is a popover that will explain what the instruction
        does when clicked and the syntax of the instruction is copied to the clipboard so that it can be quickly
        pasted into the code area.
    </p>
    <h4>Linking page elements to AngularJS assembler computer</h4>
    <p>
        The buttons, outputs, and source code box are all linked to the AngularJS assembler computer through
        <code>ng-*</code> data tags, which specify to angular what to do with the HTML element, like bind a value to
        it or loop through list values.
    </p>
    <h4>Showing the correct buttons for the current running mode</h4>
    <p>
        When the page is loaded the URL search parameters are parsed in JavaScript. If there are no parameters then
        the normal buttons should be shown for using the computer and posting code submissions. If the parameter
        <code>lesson_id</code> is present then upvote, downvote, and check buttons are added as well as a section
        for lessons. If the parameter <code>code_submission_id</code> is present then upvote, downvote, and check
        buttons are added as well as a section for lessons.
    </p>
    <h4>Adding new outputs to the assembler computer</h4>
    {!! $fig('outputs', 1600, 633, 'The outputs panel: a row of text output cells, the 20 by 20 display and three 7-segment displays', 'The outputs panel.') !!}
    <p>
        The outputs all have different ways of transforming memory into something visible. The text output just
        turns the number value in memory into its corresponding ASCII character. The canvas display uses this
        function to loop through the memory of the output and assign each addressable byte to a 5x5 block on the
        100x100 display, giving a resolution of 20x20.
    </p>
    {!! $fig('canvas-output-code', 1600, 1180, 'JavaScript function updateCanvas that loops over display memory, masks each byte into red, green and blue values and fills a 5 by 5 rectangle on the canvas', 'The function that draws the display output.') !!}
    <p>
        Bit masking is used to separate the parts of the int into separate colour bands. The value after the mask
        is then divided by the (2^numofbits)-1, to get the fraction of the max colour value, this fraction is
        multiplied by 255 to get the standard RGB value. The seven-segment displays use each bit in the lowest 8
        bits of a memory region to signify a section being turned on or off, so the following function applies and
        removes CSS classes depending on masked bit values.
    </p>
    {!! $fig('seven-segment-code', 1600, 1064, 'JavaScript that reads each 7-segment display byte from memory and adds or removes the segment-active CSS class on each segment according to its bit', 'The function that drives the 7-segment displays.') !!}
    <h4>Adding popovers to the outputs</h4>
    <p>
        The popovers help demonstrate to the user what the output does and give examples of input to the expected
        output.
    </p>
    {!! $fig('text-output-popover', 1600, 1260, 'Text Output popover: memory region 596 to 619, memory region size 24, shows the characters with the same ASCII code as the value', 'Text output popover.', 'max-w-md') !!}
    <p>The display output popover shows all the colours that can be displayed by the output.</p>
    <div class="flex flex-col sm:flex-row gap-4 items-start">
        {!! $fig('display-output-popover', 1402, 1588, 'Display Output popover: memory region 620 to 1019, size 400, explaining the 3-3-2 bit colour format with a palette of every colour', 'Display output popover.', 'max-w-md sm:w-1/2') !!}
        {!! $fig('seven-segment-popover', 1582, 1756, '7 Segment Outputs popover: memory region 1020 to 1022, size 3, with a selector that lights the matching segment on an example digit', '7-segment outputs popover.', 'max-w-md sm:w-1/2') !!}
    </div>
    <p>When the select value is changed different parts of the display light up according to the value.</p>
    <h4>Increasing the computer's addressable byte size to allow for a larger memory size</h4>
    <p>
        The original byte size for the Schweiger assembly computer was 8 bits. However, to accommodate for larger
        outputs and program memory the size was increased to 16 bits. This change only changed a few logical
        statements from checking for 256 to 65536. This functionality could have been achieved with higher and
        lower registers, but it would add unnecessary complexity.
    </p>

    <h3 id="prototype-5">Prototype 5 &ndash; Creation of Lessons</h3>
    <p>
        Having the ability to add lessons to the project as an admin allows the site to continue expanding the
        areas of teaching within assembly language far after the end of this project.
    </p>
    <h4>Lesson types</h4>
    <p>Lessons have been split up into separate types for the user to make a selection of lessons an easier process.</p>
    <h4>Lesson section</h4>
    {!! $fig('lesson-section', 1600, 799, 'The computer page during a lesson, with the lesson steps and hints shown beside the code box and outputs', 'The computer page with the lesson section open.') !!}
    <p>The lessons section is populated upon the loading of the page if the <code>lesson_id</code> exists.</p>
    <h4>Lessons page</h4>
    <p>Most of the functionality is ported over from the code submissions page except username searching has been replaced by lesson types.</p>
    <h4>Lesson creation modal</h4>
    <p>Follows all the fields required by the objectives.</p>
    {!! $fig('lesson-creation-modal', 1600, 1853, 'Post Lesson dialog with fields for type, name, description, method, three hints and the memory pattern', 'The lesson creation modal.', 'max-w-sm') !!}
    <h4>Checking for completion</h4>
    <p>
        When a user finishes a lesson, they may click the check button. When this is done the code snippet below is
        run to find out if the output pattern of the lesson is met in memory using pattern scanning. Negative 1 is
        used as a wild card value, meaning when the pattern contains a negative 1 the value is memory is ignored and
        the next bit for scanning is used so for example, the pattern &ldquo;23 -1 56&rdquo; would return true for
        both &ldquo;23 44 56&rdquo; and &ldquo;23 22 56&rdquo;.
    </p>
    {!! $fig('lesson-completion-code', 1600, 876, 'JavaScript that scans the computer memory for the lesson pattern, treating -1 as a wildcard', 'Pattern scanning to check a lesson is complete.') !!}
    <h4>Validation</h4>
    <p>Lengths for the various fields are checked before the form can be submitted.</p>
    <h4>Security</h4>
    <p>
        All the forms except the method have their HTML tags stripped. Since only admins can post lessons the
        likelihood of a lesson containing XSS is very slim.
    </p>

    <h3 id="testing">Testing &amp; Results</h3>
    <h4>Global testing methods</h4>
    <p>
        Whenever PHP pages were being created, <code>ini_set('display_errors',1)</code> and
        <code>error_reporting(E_ALL)</code> were used to show errors in the response. To test for XSS vulnerability,
        I made dummy posts and lessons to the server with Javascript Elements in them and loaded the posts and
        lessons on the site to check for execution, thankfully none of the fields worked. For all feedback on UI
        design the form from Anonymous Examples of typical data in the appendix was used.
    </p>
    <h4>Mobile and desktop</h4>
    <p>
        Throughout the UI development process, testing on different screens and sizes was performed. Using the
        &ldquo;Toggle Device Emulation&rdquo; of chrome. From this, it was discovered that on mobile devices the UI
        was staying the same layout as on desktop even though relative sizes with &ldquo;em&rdquo; values were
        honoured. This was because column ratios were not set for all sizes of the screen in bootstrap.
    </p>
    <h4>Prototype 1</h4>
    <p>
        The individual prototype UIs were shown to 10 test users each and the users were asked to vote upon which
        UI design they liked the most. This did not make use of the System Usability Scale form from the Appendix.
        But was more like a litmus test of which design worked. The outcome of this was that computer prototype 2
        was chosen for the desktop and 1 was chosen for mobiles and the community &amp; lesson prototypes chosen for
        both mobile and desktop were both 1.
    </p>
    <h4>Prototype 2</h4>
    <p>
        All aspects of the login system were tested individually. With the three test users assigned to attempting,
        registration, login, logout, and account deletion. Upon first testing, the logout system did not work
        correctly as the PHP was attempting to destroy the session before even starting it. This was fixed by adding
        <code>session_start()</code>. The first iteration also allowed admins to delete their accounts in the
        account section but in future updates the message &ldquo;Admins may not delete their accounts! Contact
        x1j44@students.keele.ac.uk to arrange manual deletion&rdquo; is shown to the user.
    </p>
    <h4>Prototype 3</h4>
    <p>
        To test prototype 3, users were asked to send submissions to the database while trying any values for the
        name, description, and code. The form validation held up and stopped strings that were too long or short but
        code submissions with quotes in their code were having the quotes replaced with htmlentities, to get around
        this the filter <code>ENT_NOQUOTES</code> is used. It was discovered that if 2 different users submitted code
        with the same name their files would overwrite each other, I got around this issue by giving the files
        random 32 character names.
    </p>
    <h4>Prototype 4</h4>
    <p>
        User testing of the display output helped me identify that during coding of the site I had swapped the
        number of bits being used for red and blue without realising and the colours being outputted to the display
        were incorrect.
    </p>
    <h4>Prototype 5</h4>
    <p>
        User testing was vital in testing this prototype as the main learning outcome is based on it. I was able to
        find an issue with the pattern finder through this user testing. This happened because there were no
        wildcards to the pattern matching so where the address was slightly different for the user and the lesson
        creator the patterns stop matching. So the parts of the pattern that could be different were replaced with
        -1. For this prototype users were given the Learning Outcome Survey (Appendix) to judge what they thought of
        the site.
    </p>

    <h3 id="evaluation">Evaluation</h3>
    <p>From the data gathered during the testing phases of development, we can give answers to the following questions.</p>
    <ol class="list-decimal pl-6 flex flex-col gap-3">
        <li>
            <p class="font-semibold">Do you feel you have gained any knowledge on how the assembly language works?</p>
            <p>
                This question was answered with great success with all ten testers agreeing that the site made them
                get a grasp on some early concepts of assembly. This question receiving a positive answer was ensured
                by earlier user testing in the developmental stages of the project.
            </p>
        </li>
        <li>
            <p class="font-semibold">If any, which exercises were you able to complete during your testing?</p>
            <p>All users reported being able to complete at least one of the exercises during the 30 minute trial period.</p>
        </li>
        <li>
            <p class="font-semibold">What do you think of the teaching methods employed? Were they engaging?</p>
            <p>
                To make this easier to answer alternative current learning methods were shown to the users as well.
                The users stated things such as &ldquo;The current sources of education on the assembly language go
                into far greater detail on the concepts being taught and therefore is richer in terms of
                content.&rdquo; However, since this project's main goal is to get people that are brand new to
                assembly programming's foot in the door. The content is sufficient to get a person interested in the
                subject.
            </p>
        </li>
    </ol>
    <p>
        The positive answers to these questions is the bare minimum for the project as without it the project would
        be meaningless. It could have been because of bad risk management/poor planning, due to a terrible UI (User
        Interface) design, or lacking UX (User Experience) such as the functionality of buttons or broken links to
        lessons. It was answered with a yes by eight out of the ten testers after 30 minutes of use and comments such
        as &ldquo;I found it to be useful to supplement in the revision of first year's systems and architecture
        module&rdquo; by TestUser1, &ldquo;first year's systems and architecture module&rdquo; is Keele University's
        first-year computer science module CSC-10033 - Systems and Architecture.
    </p>
    <p>
        Overall the project has gone to plan, even with its revisions to the original idea. This is down to the
        vigorous planning process, which kept the project guided and on track, although some personally set
        deadlines for prototypes were not met on time due to external factors, this wasn't a great issue though as
        due to planning an extra prototyping period after the rest of the prototypes just to clean up any problems
        that could be used. To take this project further, things that need to be done are the addition of more
        lessons to the site rather than just basic functionality. The code should be refactored to some extent as
        some things would work better as loops in functions rather than lists of the same copied and altered lines.
        The lessons should also alter the site to a greater extent like highlighting sections to attract the
        attention of the user.
    </p>

    <h3 id="conclusion">Conclusion</h3>
    <p>
        Belongingness and love needs; it is most certainly a stretch to say that an assembly learning website could
        fulfil these needs however by bringing in a community aspect to the site where users can interact with each
        other via voting on each other's posts and running each other's assembly code on the site; watching it alter
        the outputs or display concepts of modern-day programming in a new light. These needs being somewhat met in
        this context of the site, it will allow a user to reach the higher levels of the Maslow hierarchy which may
        not have previously been accessible (Esteem needs and Self-actualisation). Esteem needs will more easily be
        met as a user gets a feeling of accomplishment by completing lessons on the site. The accomplishment will
        come from the ability to see what lessons you have done previously so you can set goals for yourself,
        whether it be to do one lesson per week or per day.
    </p>
    <p>
        The voting system encourages users to put more time and effort into their submissions so they can gain
        higher levels of validation in their skills from higher ratings. This increases user engagement further as
        well as social interaction. Social interaction has a massive effect on learning, by increasing critical
        thinking and problem-solving skills. The highest level of motivation is noted as
        &ldquo;Self-actualization&rdquo; which is achieving one's full potential. This could be achieved by a user of
        this site once all of the content is completed. However, this is as yet untested due to no long-term user
        testing being carried out en masse.
    </p>
    <p>
        With the tools laid out for the user by this site, they should be able to forge their own meaning as to what
        assembler language is to them. They will come up with their reasons for liking or disliking it, all that
        matters is they got some experience with a concept that is becoming less and less prevalent.
    </p>
    <p>
        I leave you the reader with this: The finished product
        <a href="https://alex-davies.com/asm-sim/" target="_blank" rel="noopener">Learn Assembler (alex-davies.com)</a>.
    </p>

    <h3 id="references">References</h3>
    <ul class="flex flex-col gap-2 text-sm leading-6 break-words">
        <li>Ethics.csc.ncsu.edu. 2022. <em>Ethics in Computing - Reverse Engineering.</em> [online] Available at: <a href="https://ethics.csc.ncsu.edu/intellectual/reverse/study.php" target="_blank" rel="noopener">https://ethics.csc.ncsu.edu/intellectual/reverse/study.php</a> [Accessed 12 April 2022].</li>
        <li>Locke, E. A., Shaw, K. N., Saari, L. M., &amp; Latham, G. P. (1981). Goal setting and task performance: 1969&ndash;1980. <em>Psychological Bulletin, 90</em>(1), 125&ndash;152. <a href="https://doi.org/10.1037/0033-2909.90.1.125" target="_blank" rel="noopener">https://doi.org/10.1037/0033-2909.90.1.125</a></li>
        <li>Stefanheule.com. 2022. <em>How Many x86-64 Instructions Are There Anyway?</em> [online] Available at: <a href="https://stefanheule.com/blog/how-many-x86-64-instructions-are-there-anyway/" target="_blank" rel="noopener">https://stefanheule.com/blog/how-many-x86-64-instructions-are-there-anyway/</a> [Accessed 12 April 2022].</li>
        <li>McLeod, S. A. (2018, May 21). <em>Maslow's hierarchy of needs.</em> Retrieved from <a href="https://www.simplypsychology.org/maslow.html" target="_blank" rel="noopener">https://www.simplypsychology.org/maslow.html</a></li>
        <li>Filgona, J., Sakiyo, J., Gwany, D. and Okoronka, A., 2020. Motivation in Learning. <em>Asian Journal of Education and Social Studies</em>, pp.16-37.</li>
        <li>Martin, M., 2021. <em>Prototyping Model in Software Engineering: Methodology, Process, Approach.</em> [online] Guru99. Available at: <a href="https://www.guru99.com/software-engineering-prototyping-model.html" target="_blank" rel="noopener">https://www.guru99.com/software-engineering-prototyping-model.html</a> [Accessed 8 November 2021].</li>
        <li>Hyde, R., 2010. <em>The art of assembly language.</em> San Francisco: No Starch Press.</li>
        <li>Hurst, B., Wallace, R., &amp; Nixon, S. B. (2013). The Impact of Social Interaction on Student Learning. <em>Reading Horizons: A Journal of Literacy and Language Arts, 52</em>(4). Retrieved from <a href="https://scholarworks.wmich.edu/reading_horizons/vol52/iss4/5" target="_blank" rel="noopener">https://scholarworks.wmich.edu/reading_horizons/vol52/iss4/5</a></li>
        <li>Macadamian. 2022. <em>When to Use Waterfall vs. Agile - Macadamian.</em> [online] Available at: <a href="https://www.macadamian.com/learn/when-to-use-waterfall-vs-agile/" target="_blank" rel="noopener">https://www.macadamian.com/learn/when-to-use-waterfall-vs-agile/</a> [Accessed 8 January 2022].</li>
    </ul>

    <h3 id="poster">Project Poster</h3>
    {!! $fig('project-poster', 1800, 2571, 'Project poster for 8-Bit Assembler Learning Tool and Community with overview, aims and objectives, methodology, progress, further work, the conceptual model, computer page prototype and Gantt chart', 'The project poster.', 'max-w-xl') !!}
    <p class="text-white/70">
        The appendices (anonymous examples of typical data, the project plan, the GDPR and ethics checklist, the
        ethical review application, and the participant information sheet, consent forms and surveys) are in the
        <a href="{{ $pdf }}" target="_blank" rel="noopener">PDF</a>.
    </p>
</article>
