<x-layout>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1>Il tuo profilo</h1>

                @if(session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control mb-3">
                    <input type="email" name="email" value="{{ $user->email }}" class="form-control mb-3">
                    <input type="password" name="password" class="form-control mb-3" placeholder="Nuova password">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </form>
            </div>
        </div>
    </div>
</x-layout>