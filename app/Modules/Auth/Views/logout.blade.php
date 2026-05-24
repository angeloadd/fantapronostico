<dialog class="modal" id="logOutModal" tabindex="-1" aria-labelledby="logOutModalLabel" aria-hidden="true">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Logout</h3>
        <p class="py-4">
            Sei sicuro di voler fare logout?
        </p>
        <div class="modal-action">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-error text-base-100">Logout</button>
            </form>
            <form method="dialog">
                <!-- if there is a button in form, it will close the modal -->
                <button class="btn btn-ghost">Chiudi</button>
            </form>
        </div>
    </div>
</dialog>
