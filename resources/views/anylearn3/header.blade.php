<header>
    <div class="bg-blue-600 border-b border-gray-200">
        <div class="mx-auto max-w-5xl py-1 flex justify-between px-2 font-medium text-xs">
            <div class="flex items-center">
                <a href="{{ route('allclasses') }}" class="text-white hover:text-gray-300 rounded-md px-2 py-1 hidden sm:block">Kênh học tập</a>
                <span class="text-white hover:text-gray-300 py-1 hidden sm:block">|</span>
                <a href="{{ route('refpage', ['code' => 'anylearn']) }}" class="text-white hover:text-gray-300 rounded-md px-2 py-1">Trở thành đối tác anyLEARN</a>
                <span class="text-white hover:text-gray-300 py-1 hidden sm:block">|</span>
                <a href="#download-app" class="text-white hover:text-gray-300 rounded-md px-2 py-1 hidden sm:block">Tải ứng dụng</a>
            </div>
            <div class="flex items-center">
                <a href="{{ route('helpcenter') }}" class="text-white hover:text-gray-300 rounded-md px-2 py-1 hidden sm:block"><i class="fa fa-bell"></i> Thông báo</a>
                <span class="text-white hover:text-gray-300 px-1 py-1 hidden sm:block">|</span>
                <a href="{{ route('helpcenter') }}" class="text-white hover:text-gray-300 rounded-md px-2 py-1 hidden sm:block"><i class="fa fa-question-circle"></i> Hỗ trợ</a>
                <a href="{{ route('helpcenter') }}" class="text-white hover:text-gray-300 rounded-md px-2 py-1 sm:hidden">Hỗ trợ</a>
                <span class="text-white hover:text-gray-300 px-1 py-1">|</span>

                @if (!Auth::user())
                    <a href="{{ route('login') }}" class="text-white px-2 py-1 rounded-md">Đăng nhập</a>
                @else
                    <a href="{{ route('auth.logout') }}" class="text-white px-2 py-1 rounded-md">Đăng xuất</a>
                @endif
            </div>
        </div>
    </div>
</header>
