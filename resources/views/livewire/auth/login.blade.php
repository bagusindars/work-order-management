<div>
    <div class="flex justify-center items-center h-screen">
        <x-form wire:submit="submit" no-separator>
            <div class="w-full md:w-[500px] md:shadow-lg mx-auto rounded-md py-7 px-8">
                <p class="font-semibold text-xl text-black">Login to your account!</p>
                <span class="my-3 block text-sm">Fill in your email address and password to enter the system.</span>
                <div class="mt-5">
                    <label class="font-semibold text-sm" for="email">Email</label>
                    <x-input wire:model="email" class=" mt-2" placeholder="johndoe@gmail.com" />
                </div>
                <div class="mt-5">
                    <label class="font-semibold text-sm" for="password">Password</label>
                    <x-input type="password" wire:model="password" class=" mt-2" placeholder="******" />
                </div>
                <x-button wire:click="submit" spinner label="Login" class="btn-primary mt-10 w-full" />
            </div>
        </x-form>
    </div>

</div>
