<x-page title={{$title}}>
    <x-content type="wide" class="flex flex-col items-center justify-center gap-6 py-6">
        <div class="flex flex-col items-center justify-center rounded-lg bg-black ring-2 ring-white p-4">
            <p id="token" class="text-white" data-token="{{$temporary_token}}">{{$temporary_token}}</p>
            <button class="bg-white text-black px-4 py-2 rounded-lg">Copy</button>
            <p class="text-white">Time left: <div id="time-left"></div></p>
        </div>
        <div id="messages" class="flex flex-col items-center justify-center gap-6 text-white">
            @foreach($messages as $message_index => $message)
                <p>{{$message_index}} - {{$message}}</p>
            @endforeach
        </div>
    </x-content>
</x-page>
