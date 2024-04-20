<div class="flex justify-start items-center w-full bg-white border-b border-neutral-200/80">
<div x-data="{
        menuBarOpen: false, 
        menuBarMenu: ''
    }" 
    @click.away="menuBarOpen=false" 
    class="relative top-0 left-0 z-50 flex-shrink-0 w-auto h-10 transition-all duration-150 ease-out"
    >
    <div class="relative top-0 left-0 z-40 w-full h-10 transition duration-200 ease-out">
        <div class="px-1.5 py-1 w-full h-full">
            <div class="flex justify-between w-full h-full select-none text-neutral-900">
                    
                <!-- File Button -->
                <div class="relative h-full cursor-default">

                    <button @click="menuBarOpen=true; menuBarMenu='file'" @mouseover="menuBarMenu='file'" :class="{ 'bg-neutral-100' : menuBarOpen && menuBarMenu == 'file'}" class="flex justify-center items-center px-3 py-1.5 h-full text-sm leading-tight rounded cursor-default hover:bg-neutral-100">
                        Pages
                    </button>
                    <div 
                        x-show="menuBarOpen && menuBarMenu=='file'" 
                        x-transition:enter="transition ease-linear duration-100" 
                        x-transition:enter-start="-translate-y-1 opacity-90" 
                        x-transition:enter-end="translate-y-0 opacity-100" 
                        class="absolute top-0 z-50 min-w-[8rem] text-neutral-800 rounded-md border border-neutral-200/70 bg-white mt-10 text-sm p-1 shadow-md w-48 -translate-x-0.5"
                        x-cloak>

                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>New Tab</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⌘T</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>New Window</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⌘N</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none" data-disabled>
                            <span>New Incognito Window</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button class="relative w-full group">
                            <div class="flex items-center px-2 py-1.5 rounded cursor-default outline-none select-none hover:bg-neutral-100">
                                <span>Share</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto w-4 h-4"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div data-submenu="" class="absolute top-0 right-0 invisible mr-1 opacity-0 duration-200 ease-out translate-x-full group-hover:mr-0 group-hover:visible group-hover:opacity-100">
                                <div class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md animate-in slide-in-from-left-1 w-32">
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Email link</div>
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Messages</div>
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Notes</div>
                                </div>
                            </div>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Print</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⌘P</span>
                        </button>
                    </div>

                </div>
                <!-- End File Button -->
            
                <!-- Edit Button -->
                <div class="relative h-full cursor-default">
                    <button @click="menuBarOpen=true; menuBarMenu='edit'" @mouseover="menuBarMenu='edit'" :class="{ 'bg-neutral-100' : menuBarOpen && menuBarMenu == 'edit'}" class="flex justify-center items-center px-3 py-1.5 h-full text-sm leading-tight rounded cursor-default hover:bg-neutral-100">
                        Edit
                    </button>
                    <div 
                        x-show="menuBarOpen && menuBarMenu=='edit'" 
                        x-transition:enter="transition ease-linear duration-100" 
                        x-transition:enter-start="-translate-y-1 opacity-90" 
                        x-transition:enter-end="translate-y-0 opacity-100"
                        class="absolute top-0 z-50 min-w-[8rem] text-neutral-800 rounded-md border border-neutral-200/70 bg-white mt-10 text-sm p-1 shadow-md w-48 -translate-x-0.5"
                        x-cloak>

                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Undo</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⌘Z</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Redo</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⇧⌘Z</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button class="relative w-full group">
                            <div class="flex items-center px-2 py-1.5 rounded cursor-default outline-none select-none hover:bg-neutral-100">
                                <span>Find</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto w-4 h-4"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div data-submenu="" class="absolute top-0 right-0 invisible mr-1 opacity-0 duration-200 ease-out translate-x-full group-hover:mr-0 group-hover:visible group-hover:opacity-100">
                                <div class="z-50 min-w-[8rem] overflow-hidden rounded-md border bg-white p-1 shadow-md animate-in slide-in-from-left-1 w-32">
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Search the web</div>
                                    <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Find...</div>
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Find Next</div>
                                    <div @click="menuBarOpen=false" class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">Find Previous</div>
                                </div>
                            </div>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Cut</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Copy</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Paste</span>
                        </button>
                    </div>
                </div>
                <!-- End Edit Button -->

                <!-- View Button -->
                <div class="relative h-full cursor-default">
                    <button @click="menuBarOpen=true; menuBarMenu='view'" @mouseover="menuBarMenu='view'" :class="{ 'bg-neutral-100' : menuBarOpen && menuBarMenu == 'view'}" class="flex justify-center items-center px-3 py-1.5 h-full text-sm leading-tight rounded cursor-default hover:bg-neutral-100">
                        View
                    </button>
                    <div 
                        x-show="menuBarOpen && menuBarMenu=='view'" 
                        x-transition:enter="transition ease-linear duration-100" 
                        x-transition:enter-start="-translate-y-1 opacity-90" 
                        x-transition:enter-end="translate-y-0 opacity-100"
                        class="absolute top-0 z-50 min-w-[15rem] text-neutral-800 rounded-md border border-neutral-200/70 bg-white mt-10 text-sm p-1 shadow-md w-48 -translate-x-0.5"
                        x-cloak>

                        <button @click="menuBarOpen=false; alwaysShowBookmarks=!alwaysShowBookmarks;" x-data="{ alwaysShowBookmarks: false }" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span x-show="alwaysShowBookmarks" class="flex absolute left-2 justify-center items-center w-3.5 h-3.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span>Always Show Bookmarks Bar</span>
                        </button>
                        <button @click="menuBarOpen=false; alwaysShowFullURL=!alwaysShowFullURL" x-data="{ alwaysShowFullURL: true }" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span x-show="alwaysShowFullURL" class="flex absolute left-2 justify-center items-center w-3.5 h-3.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                            <span>Always Show Full URLs</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Reload</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⌘R</span>
                        </button>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none" data-disabled>
                            <span>Force Reload</span>
                            <span class="ml-auto text-xs tracking-widest text-neutral-400 group-hover:text-neutral-600">⇧⌘R</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Toggle Fullscreen</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Hide Sidebar</span>
                        </button>
                    </div>
                </div>
                <!-- End View Button -->

                <!-- Profiles Button -->
                <div class="relative h-full cursor-default">
                    <button @click="menuBarOpen=true; menuBarMenu='profiles'" @mouseover="menuBarMenu='profiles'" :class="{ 'bg-neutral-100' : menuBarOpen && menuBarMenu == 'profiles'}" class="flex justify-center items-center px-3 py-1.5 h-full text-sm leading-tight rounded cursor-default hover:bg-neutral-100">
                        Profiles
                    </button>
                    <div 
                        x-show="menuBarOpen && menuBarMenu=='profiles'" 
                        x-transition:enter="transition ease-linear duration-100" 
                        x-transition:enter-start="-translate-y-1 opacity-90" 
                        x-transition:enter-end="translate-y-0 opacity-100"
                        class="absolute top-0 z-50 min-w-[8rem] text-neutral-800 rounded-md border border-neutral-200/70 bg-white mt-10 text-sm p-1 shadow-md w-48 -translate-x-0.5"
                        x-cloak>

                        <div class="relative w-full">
                            <button @click="menuBarOpen=false" class="relative w-full flex cursor-default select-none items-center rounded py-1.5 pl-8 pr-2 hover:bg-neutral-100 outline-none data-[disabled]:opacity-50">
                                <span class="flex absolute left-2 justify-center items-center w-3.5 h-3.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-2 h-2 fill-current"><circle cx="12" cy="12" r="10"></circle></svg></span>
                                <span>Taylor Otwell</span>
                            </button>
                            <button @click="menuBarOpen=false" class="relative w-full flex cursor-default select-none items-center rounded py-1.5 pl-8 pr-2 hover:bg-neutral-100 outline-none data-[disabled]:opacity-50">
                                <span>Adam Wathan</span>
                            </button>
                            <button @click="menuBarOpen=false" class="relative w-full flex cursor-default select-none items-center rounded py-1.5 pl-8 pr-2 hover:bg-neutral-100 outline-none data-[disabled]:opacity-50">
                                <span>Caleb Porzio</span>
                            </button>
                        </div>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Edit...</span>
                        </button>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <button @click="menuBarOpen=false" class="relative flex justify-between w-full pl-8 cursor-default select-none group items-center rounded px-2 py-1.5 hover:bg-neutral-100 hover:text-neutral-900 outline-none data-[disabled]:opacity-50 data-[disabled]:pointer-events-none">
                            <span>Add Profile...</span>
                        </button>
                    </div>
                </div>
                <!-- End Profiles Button -->
            </div>     
        </div>
    </div>
</div>
</div>