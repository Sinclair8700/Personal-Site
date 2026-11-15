{{-- Write-up for /projects/lil-chongee-bud-bro. Renders below the project description. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>The game</h2>
    <p>
        Lil Chongee's job is chilling. In the first act you work through missions for each of your Bud Bros, and
        each one unlocks the next of eight districts, from your home block through the strip mall, campus,
        downtown and the docks to a festival, on the way to finding The Plug. His hideout is one of four vans and
        changes with every save, so you narrow it down with clues. In the second act you become the Plug yourself:
        customers walk up to you, you set prices and haggle, and you have to spot the narcs among them.
    </p>

    <h2>Systems</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Sativa and Indica bars:</strong> they drain depending on what you're doing and where you are. Sativa sets your speed, stamina and how wide your view cone is; Indica slows Sativa's drain and improves loot.</li>
        <li><strong>Feds:</strong> beat cops with vision cones and line of sight, who chase, search and investigate. K9 units follow your smell, undercover narcs blend in, downtown has cameras you can spray-paint, and rising Heat brings a drone and then SWAT roadblocks.</li>
        <li><strong>Busts and jail:</strong> penalties scale with how much you're carrying, and every fifth bust is jail, with four ways to break out.</li>
        <li><strong>Economy:</strong> three currencies that your Bros exchange, with fees that drop as your Vibes with them rise; seven dealers, five shops and an online market with listings that might be a sting.</li>
        <li><strong>Missions:</strong> 24 missions of 14 types, from timed deliveries and losing a tail to spotting the narc and hauling cargo.</li>
    </ul>

    <h2>Two-player co-op</h2>
    <p>
        One player hosts as Chongee and the other joins as the Bud-Bro, over a local network or the internet,
        using Godot's ENet networking. The world, time, weather, Heat, missions and the hunt for The Plug are
        shared, while each player keeps their own bars, money, Vibes and jail record. The host's save holds the
        world and each player's own progress saves separately. There are co-op moves too: passing the bag to your
        partner before a Fed reaches you, keeping lookout while they deal, and breaking them out of jail.
    </p>

    <h2>How it's made</h2>
    <p>
        The 8-bit look comes from rendering the 3D world at 480&nbsp;&times;&nbsp;270 and scaling it up by whole
        numbers, with a post-processing shader for screen grain. The map is built in code, one script per
        district, rather than placed by hand in the editor. There are no audio files at all: every sound effect
        and the chiptune music are synthesised while the game runs, and the music changes when you're being
        chased. On Macs the game switches to Godot's OpenGL renderer, because on Intel Macs the Vulkan renderer
        runs through a translation layer where some of its shaders fail and the lighting breaks.
    </p>

    <h2>Testing and releases</h2>
    <p>
        Running the game with <code>--autotest</code> plays a scripted pass through every system and checks
        about 250 things, exiting with an error if any fail. A separate test starts a host and a client as two
        processes and checks co-op end to end. On every push, GitHub Actions exports Windows, macOS and Linux
        builds, runs the autotest on each exported build on real Windows, Ubuntu and macOS machines along with
        the co-op test, and publishes a release when I tag a version. The whole pipeline takes about two and a
        half minutes.
    </p>

    <h2>Built with</h2>
    <p>
        Godot 4.7, GDScript (around 32,000 lines), ENet and GitHub Actions.
    </p>
</article>
