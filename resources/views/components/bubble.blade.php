<div {{ $attributes->merge(['class' => 'w-full border-2 bg-black border-white transition-[colors,_scale] duration-300 focus-within:border-purple hover:border-purple px-6 py-4 rounded-lg [&_ul]:text-white [&_ul]:text-[16px] [&_ul]:leading-[24px] [&_ul]:list-disc [&_ul]:list-inside
    [&_h3]:text-white [&_h3]:text-[24px] [&_h3]:leading-[32px]  [&_h3]:mb-2 hover:scale-[1.05] origin-center']) }}>
    {{ $slot }}
</div>