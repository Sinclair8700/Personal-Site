<form action="{{ route('login') }}" method="POST">
    @csrf
    <x-input type="text" name="email" placeholder="Email"/>
    <x-input type="password" name="password" placeholder="Password"/>
    <button type="submit">Sign In</button>
</form>