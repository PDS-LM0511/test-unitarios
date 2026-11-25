<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de precios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 px-4 py-10 text-slate-900">
    <main class="mx-auto max-w-3xl">
        <h1 class="mb-8 text-3xl font-bold">Calculadora de precios</h1>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($result !== null)
            <div class="mb-6 rounded-lg bg-emerald-100 p-4 text-emerald-900" role="status">
                <span class="font-medium">{{ $resultLabel }}:</span>
                <strong>{{ number_format($result, 2, ',', '.') }}</strong>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-xl bg-white p-6 shadow">
                <h2 class="mb-4 text-xl font-semibold">Conversión EUR / USD</h2>
                <p class="mb-4 text-sm text-slate-600">Tipo fijo: 1 EUR = 1,10 USD</p>
                <form method="POST" action="{{ route('calculator.calculate') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="operation" value="conversion">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">Importe</span>
                        <input type="number" name="amount" min="0" step="0.01" value="{{ old('amount') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium">De</span>
                            <select name="from" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                <option value="EUR" @selected(old('from', 'EUR') === 'EUR')>EUR</option>
                                <option value="USD" @selected(old('from') === 'USD')>USD</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1 block text-sm font-medium">A</span>
                            <select name="to" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                <option value="USD" @selected(old('to', 'USD') === 'USD')>USD</option>
                                <option value="EUR" @selected(old('to') === 'EUR')>EUR</option>
                            </select>
                        </label>
                    </div>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 font-medium text-white hover:bg-blue-800">
                        Convertir
                    </button>
                </form>
            </section>

            <section class="rounded-xl bg-white p-6 shadow">
                <h2 class="mb-4 text-xl font-semibold">Cálculo de descuento</h2>
                <form method="POST" action="{{ route('calculator.calculate') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="operation" value="discount">
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">Precio (€)</span>
                        <input type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium">Descuento (%)</span>
                        <input type="number" name="discount_percentage" min="0" max="100" step="0.01"
                            value="{{ old('discount_percentage') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </label>
                    <button class="rounded-lg bg-blue-700 px-4 py-2 font-medium text-white hover:bg-blue-800">
                        Calcular descuento
                    </button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
