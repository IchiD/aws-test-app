<template>
    <div class="logo-container flex items-center space-x-2">
        <!-- SVGグラデーション定義 -->
        <svg width="0" height="0" class="hidden">
            <defs>
                <linearGradient
                    id="gradient"
                    x1="0%"
                    y1="0%"
                    x2="100%"
                    y2="100%"
                >
                    <stop offset="0%" stop-color="#6366f1" />
                    <stop offset="50%" stop-color="#a855f7" />
                    <stop offset="100%" stop-color="#ec4899" />
                </linearGradient>
            </defs>
        </svg>

        <!-- モダンなアイコン -->
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="logo-icon"
            width="30"
            height="30"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"
            ></path>
        </svg>
        <!-- アプリ名 -->
        <span
            class="font-bold text-xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"
            >DiaryTasks</span
        >
    </div>
</template>

<style scoped>
.logo-container {
    transition: all 0.3s ease;
}
.logo-container:hover {
    transform: scale(1.05);
}
.logo-icon {
    stroke: currentColor;
    stroke-width: 2;
    transition: all 0.3s ease;
}
.logo-container:hover .logo-icon {
    stroke: url(#gradient);
}
</style>
