<div>
    <form wire:submit.prevent="submit" action="#" method="post">
        <input wire:model="name" type="text">
        <button type="submit">Submit</button>
        @if ($success)
            <div>Saved</div>
        @endif
    </form>
</div>
