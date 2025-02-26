<div {{ $attributes->merge(['class' => 'w-full border-2 border-white px-6 py-4 rounded-md [&_ul]:text-white [&_ul]:text-[16px] [&_ul]:leading-[24px] [&_ul]:list-disc [&_ul]:list-inside
    [&_h3]:text-white [&_h3]:text-[24px] [&_h3]:leading-[32px]  [&_h3]:mb-2']) }}>
    {{ $slot }}
</div>