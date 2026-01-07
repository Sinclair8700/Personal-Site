<form action="{{ route('register') }}" method="POST">
    @csrf
    <x-input type="text" name="name" placeholder="Name"/>
    <x-input type="email" name="email" placeholder="Email"/>
    <x-input type="password" name="password" placeholder="Password"/>
    <x-input type="password" name="password_confirmation" placeholder="Confirm Password"/>
    <button type="submit">Sign Up</button>
</form>