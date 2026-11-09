{{-- Write-up for /projects/pi-power-button. Renders below the project description. --}}
<article class="text-white max-w-3xl mt-10 flex flex-col gap-4 leading-7
    [&_h2]:mt-6 [&_code]:bg-white/10 [&_code]:px-1 [&_code]:rounded [&_code]:text-[0.9em]
    [&_a]:underline [&_a:hover]:text-purple">

    <h2>How it works</h2>
    <p>
        A Raspberry Pi Zero W sits next to my PC. One of its GPIO pins drives a relay module, and the relay's
        contacts are wired across the two <code>PWR_SW</code> pins on the motherboard's front-panel header, the
        same two pins the case's power button connects to. When the relay closes, the motherboard sees exactly
        what it would see if someone pressed the button. Everything else (the web page, the schedules, the
        status) is one Python program on the Pi that uses nothing but the standard library.
    </p>

    <ol class="flex flex-col md:flex-row md:items-stretch gap-1 md:gap-0 my-2" aria-label="How a request reaches the PC">
        <li class="flex-1 border-2 border-white rounded-lg px-3 py-2 bg-black">
            <p class="font-bold">Phone</p>
            <p class="text-sm text-white/70">web app, Siri Shortcut or widget</p>
        </li>
        <li aria-hidden="true" class="self-center text-center text-white/60 text-xs md:px-2">
            <span class="md:hidden">&darr;</span><span class="hidden md:inline">&rarr;</span><br>HTTPS
        </li>
        <li class="flex-1 border-2 border-white rounded-lg px-3 py-2 bg-black">
            <p class="font-bold">My server</p>
            <p class="text-sm text-white/70">Apache reverse proxy</p>
        </li>
        <li aria-hidden="true" class="self-center text-center text-white/60 text-xs md:px-2">
            <span class="md:hidden">&darr;</span><span class="hidden md:inline">&rarr;</span><br>SSH tunnel
        </li>
        <li class="flex-1 border-2 border-white rounded-lg px-3 py-2 bg-black">
            <p class="font-bold">Pi Zero W</p>
            <p class="text-sm text-white/70">Python web server</p>
        </li>
        <li aria-hidden="true" class="self-center text-center text-white/60 text-xs md:px-2">
            <span class="md:hidden">&darr;</span><span class="hidden md:inline">&rarr;</span><br>GPIO 23
        </li>
        <li class="flex-1 border-2 border-purple rounded-lg px-3 py-2 bg-black">
            <p class="font-bold">Relay</p>
            <p class="text-sm text-white/70">closes for 0.4&nbsp;s or 6&nbsp;s</p>
        </li>
        <li aria-hidden="true" class="self-center text-center text-white/60 text-xs md:px-2">
            <span class="md:hidden">&darr;</span><span class="hidden md:inline">&rarr;</span><br>PWR_SW
        </li>
        <li class="flex-1 border-2 border-white rounded-lg px-3 py-2 bg-black">
            <p class="font-bold">PC</p>
            <p class="text-sm text-white/70">turns on or off</p>
        </li>
    </ol>

    <h2>Pressing the button with a relay</h2>
    <p>
        A motherboard's power input is a momentary switch: briefly connecting the two <code>PWR_SW</code> pins
        tells it to act, and how long they stay connected decides what it does. A relay is a good fit because
        the Pi never touches the PC's circuitry. The Pi only switches the relay's input, and the PC only sees a
        pair of contacts close, so the two machines stay electrically separate.
    </p>
    <p>
        The relay's input is wired to GPIO 23 (physical pin 16). The server drives the pin with
        <code>raspi-gpio</code>, or <code>pinctrl</code> on newer Raspberry Pi OS images:
    </p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Press:</strong> <code>raspi-gpio set 23 op</code> (make it an output, only if it isn't one already), then <code>raspi-gpio set 23 dh</code> to drive it high, which closes the relay.</li>
        <li><strong>Release:</strong> <code>raspi-gpio set 23 dl</code> drives it low and the relay opens.</li>
    </ul>
    <p>
        Every action is a timed <em>pulse</em>. The release sits in a <code>finally</code> block, so the relay
        always opens again even if something goes wrong mid-press:
    </p>
<pre class="bg-white/5 border border-white/20 rounded-lg p-4 overflow-x-auto text-sm leading-6"><code class="!bg-transparent !p-0">def pulse(self, seconds):
    """Hold the switch for a fixed time, releasing even if something goes wrong."""
    self.press()
    try:
        time.sleep(max(0.05, float(seconds)))
    finally:
        self.release()</code></pre>
    <p>
        As a second safety net, a background thread checks once a second whether the pin has been held for
        longer than 30 seconds (which can happen with the manual hold button on the page) and releases it if so.
        Only one power action can run at a time, so two taps can't overlap and accidentally become a long hold.
    </p>

    <h2>Turning the PC on and off</h2>
    <p>The same relay does every job. Only the length of the press changes:</p>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b-2 border-white">
                    <th class="py-2 pr-4">Action</th>
                    <th class="py-2 pr-4">Press</th>
                    <th class="py-2">What the PC does</th>
                </tr>
            </thead>
            <tbody class="[&_td]:py-2 [&_td]:pr-4 [&_td]:align-top [&_tr]:border-b [&_tr]:border-white/20">
                <tr><td>Turn on</td><td>0.4&nbsp;s tap</td><td>From off, the power supply starts and the PC boots. From sleep, it wakes up.</td></tr>
                <tr><td>Shut down</td><td>0.4&nbsp;s tap</td><td>While Windows is running, a short press is an ACPI power-button event, and Windows does whatever its power button is set to do, which by default is a clean shutdown.</td></tr>
                <tr><td>Force off</td><td>6&nbsp;s hold</td><td>Holding the switch for more than about 4 seconds makes the motherboard cut the power no matter what the OS is doing. This is the fix for a frozen PC.</td></tr>
                <tr><td>Wake</td><td>no press</td><td>A Wake-on-LAN magic packet (6 &times; <code>0xFF</code> followed by the MAC address 16 times), sent to the broadcast addresses and the PC. It's an alternative to the tap when the PC is asleep.</td></tr>
            </tbody>
        </table>
    </div>
    <p>
        Because "turn on" and "shut down" are the same tap, pressing it blind would be risky: the page has to
        know whether the PC is already on before it does anything. That's what the next part is for.
    </p>

    <h2>Knowing whether it worked</h2>
    <p>Every five seconds the Pi checks the PC over the local network:</p>
    <ul class="list-disc pl-6 flex flex-col gap-1">
        <li><strong>Off:</strong> nothing answers at its address.</li>
        <li><strong>Asleep:</strong> the network card answers ARP but the PC doesn't answer ping. With Wake-on-LAN enabled, Windows leaves the card listening while it sleeps.</li>
        <li><strong>On:</strong> it answers ping.</li>
        <li><strong>Ready:</strong> a TCP connection to a port Windows itself listens on (Remote Desktop, file sharing and similar) succeeds, so Windows has actually finished booting, not just the network card.</li>
    </ul>
    <p>
        The ARP and ICMP checks use raw sockets. Instead of running the server as root, systemd gives it only
        the <code>CAP_NET_RAW</code> capability. If the PC's IP address changes, the Pi finds it again by its
        MAC address.
    </p>
    <p>
        After a press, the action stays open until the PC reaches the state it should be in, or until a timeout
        (4 minutes for turning on, 1 minute for forcing off) says it didn't. A successful boot ends with
        something like <em>"Ready after 41 s (pinging after 12 s)"</em>, and those times go into a history of
        boots and power-on hours. A timeout ends with a reason, for example "Check the switch wiring" or
        "Windows may be asking about unsaved work".
    </p>

    <h2>The other half: a helper on the PC</h2>
    <p>
        The relay can only press the button. For everything that has to happen inside Windows (sleep,
        hibernate, restart, lock, a clean shutdown with no button at all), a small PowerShell script runs hidden
        on the PC. It only ever connects <em>out</em> to the Pi: every few seconds it reports who is logged in,
        how long the PC has been idle, and CPU, GPU and memory load, and it waits for commands.
        This also makes auto-off possible: once nobody has touched the PC for two hours, nothing like a film is
        keeping the screen on, and the CPU and GPU are quiet, the Pi tells Windows to shut down.
    </p>

    <h2>Reaching it from outside the house</h2>
    <p>
        The Pi is on home Wi-Fi behind the router, so nothing reaches it directly. Instead it keeps a reverse SSH
        tunnel open to my server. Apache there has a subdomain with a Let's Encrypt certificate and passes its
        requests down the tunnel to the Pi. The tunnel's SSH key is locked down on the server so it can only
        open those forwards, with no shell. The page itself needs a password (stored as a PBKDF2 hash),
        logins are kept in signed cookies, and Siri Shortcuts and home-screen widgets use API tokens that can
        be revoked individually.
    </p>

    <h2>Keeping a Pi Zero online</h2>
    <p>
        The Zero W's Wi-Fi was the weakest link, so the installer turns off Wi-Fi power saving (the chip drops
        off the network with it on), adds a watchdog that reconnects Wi-Fi when the router disappears, and turns
        on the hardware watchdog: systemd checks in with <code>/dev/watchdog</code> every 7 seconds, and if the
        Pi ever hangs it reboots itself after 14. History is written to the SD card in batches rather than
        on every change, to save wear on the card.
    </p>

    <h2>Built with</h2>
    <p>
        Python 3 (standard library only: <code>http.server</code>, raw sockets, <code>hashlib</code>),
        <code>raspi-gpio</code>, systemd, a vanilla JavaScript web app that can be installed on a phone's home
        screen, PowerShell, OpenSSH and Apache.
    </p>
</article>
