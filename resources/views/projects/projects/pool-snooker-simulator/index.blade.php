{{-- Write-up for /projects/pool-snooker-simulator. Renders below the project description. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>The brief</h2>
    <p>
        This was my coursework for the Games Computing module at Keele University in January 2022: design a game,
        build it in C++ on openFrameworks with the Open Dynamics Engine (ODE) for physics, then evaluate it. I
        weighed up pinball, air hockey and cue sports, and went with cue sports because they are played with
        friends, and because pool needs physics accurate enough to really test what ODE can do. My twist was
        letting players change the game itself: switch individual rules off, tweak the table, and turn on two
        new modes that hand out random rewards and punishments.
    </p>

    <h2>Setting up a game</h2>
    <p>The start menu, built with openFrameworks' <code>ofxGui</code>, lets the players choose:</p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Pool or snooker.</strong> Each game builds its own table and ball layout.</li>
        <li><strong>Player names.</strong> Player 1 always breaks.</li>
        <li><strong>Table settings:</strong> maximum cue power, and offsets for how slippery the cloth is and how bouncy the balls are.</li>
        <li><strong>Golden Pocket and Evil Pocket</strong>, the two new modes (below).</li>
        <li>
            <strong>Which pool fouls count.</strong> Each of these can be switched off: potting the white, potting the
            wrong colour, hitting the wrong colour first, missing everything, hitting no cushion and potting nothing,
            and knocking the white off the table. With "potting the white" off, it becomes a tactic: pot it on purpose
            and your opponent has to play from behind the baulk line.
        </li>
    </ul>

    <h2>The rules</h2>
    <p>Pool follows the English 8-ball rules I grew up playing. After every shot, once all the balls have stopped, the game checks:</p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Fouls</strong> (any that are switched on). A foul gives the other player two visits, or one visit with a free white if they are on the black.</li>
        <li><strong>The baulk line</strong>, drawn across the table a fifth of its length from the end. A free white can be moved anywhere behind it with W, A, S and D, and is pushed back if it strays out.</li>
        <li><strong>Colours.</strong> The first ball potted cleanly after the break decides who is reds and who is yellows.</li>
        <li><strong>Winning.</strong> Pot all your colour, then the black. Pot the black early, or foul while potting it, and your opponent wins.</li>
    </ul>
    <p>Fouls and wins are announced on screen, so new players can see why their opponent just got two visits.</p>

    <h2>Golden Pocket and Evil Pocket</h2>
    <p>
        Turning either mode on highlights a random pocket. Pot a ball into it without fouling and something random
        happens, then the highlight moves to a different pocket:
    </p>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b-2 border-white">
                    <th class="py-2 pr-4">Golden Pocket (a reward)</th>
                    <th class="py-2">Evil Pocket (a punishment)</th>
                </tr>
            </thead>
            <tbody class="[&_td]:py-2 [&_td]:pr-4 [&_td]:align-top [&_tr]:border-b [&_tr]:border-white/20">
                <tr><td>An extra visit on top of the one for potting</td><td>You lose the visit you just earned by potting</td></tr>
                <tr><td>A free white you can move behind the baulk line</td><td>Random cloth friction for the next shot</td></tr>
                <tr><td>One fewer of your balls to pot before the black</td><td>One fewer of your opponent's balls to pot before the black</td></tr>
            </tbody>
        </table>
    </div>
    <p>
        The randomness is the point. It opens up more possible games, so the game is harder to "solve" and takes
        longer to get boring.
    </p>

    <h2>Playing a shot</h2>
    <p>
        The cue, which I modelled and UV-textured in Blender, orbits the white ball. Its position is just sine and
        cosine of the aim angle, and it turns to face the ball. A guideline shows where the white will go. That
        matters on a screen, because you can't walk round the table to look. There are three cameras: above the
        table, down the cue, and a free camera.
    </p>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b-2 border-white">
                    <th class="py-2 pr-4">Key</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody class="[&_td]:py-2 [&_td]:pr-4 [&_td]:align-top [&_tr]:border-b [&_tr]:border-white/20">
                <tr><td>&larr; / &rarr;</td><td>Rotate the cue around the white</td></tr>
                <tr><td>Tab</td><td>Switch between coarse and fine aiming</td></tr>
                <tr><td>&uarr; / &darr;</td><td>Increase or decrease shot power</td></tr>
                <tr><td>1 / 2 / 3</td><td>Above view, cue view, free camera</td></tr>
                <tr><td>W A S D</td><td>Move a free white ball</td></tr>
                <tr><td>Space</td><td>Take the shot</td></tr>
            </tbody>
        </table>
    </div>
    <p>
        Taking the shot runs a short animation: the power bar drains as the cue moves forward and strikes the
        white, and only then is the force applied to the ball.
    </p>

    <h2>Physics with ODE</h2>
    <p>
        Balls bounce off the cushions using ODE's <code>dContactBounce</code>. A pot is detected with
        <code>dCollide</code> between a ball and a pocket's geometry, so the ball drops into the pocket instead of
        vanishing on contact.
    </p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li>
            <strong>Friction.</strong> ODE has no rolling friction, so a rolling ball would never stop. I damp every
            ball's velocity by hand each step.
        </li>
        <li>
            <strong>Spin.</strong> My first version used <code>dContactRolling</code> on the cloth to model spin.
            Balls then drifted left or right off their line without touching anything, and sped up and slowed down
            at random. I swapped it for slip contacts on the cloth, locked each ball's angular velocity to zero, and
            kept the hand-written damping. Making the balls bigger and denser changed the drift a lot, which points
            to the simulation's scale as the real cause.
        </li>
        <li>
            <strong>A stable step.</strong> The frame rate varies, so the game tracks the time that has passed and
            runs as many fixed-size <code>dWorldStep</code> calls as fit into it.
        </li>
        <li>
            <strong>Ending a turn.</strong> A turn starts when the white is struck and ends when every ball has
            stopped. A ball counts as stopped only after it has stayed slow for a minimum number of ticks, so a
            ball that pauses against a cushion doesn't end the turn early.
        </li>
    </ul>

    <h2>How the code is organised</h2>
    <p>
        Everything hangs off openFrameworks' <code>setup</code>, <code>update</code> and <code>draw</code>.
        <code>setup</code> builds the menus, camera, lighting and the ODE world. <code>update</code> steps the
        physics. <code>draw</code> runs the game's drawing and rule logic. <code>Game</code> is an abstract base
        class that holds the players, fonts, settings and turn logic. <code>PoolGame</code> and
        <code>SnookerGame</code> inherit from it and set up their own tables and ball positions, so a new variant
        such as 9-ball only needs a new subclass. When a game ends, the winner's name pulses through the colours
        on screen, the game's memory is freed, and play returns to the start menu.
    </p>

    <h2>What I'd fix next</h2>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li>Balls leave the cushions at a slightly shallower angle than they should. I suspect this comes from locking their spin.</li>
        <li>The cue and the guideline pass through the cushions. The fix is to clip the guideline where it meets a cushion and lift the back of the cue over it.</li>
        <li>Spin (side, top and screw) is missing, and adding it means scaling the whole simulation up first.</li>
    </ul>

    <h2>Built with</h2>
    <p>
        C++, openFrameworks with <code>ofxGui</code>, the Open Dynamics Engine, and Blender for the cue model.
        It's about 2,350 lines of my own code across 25 source files, built on Linux.
    </p>
</article>
