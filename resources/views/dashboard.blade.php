@extends('layouts.admin')

@section('content')
    <div class="grid grid-cols-4 gap-8">

        <div class="bg-white rounded-3xl p-8">

            <h3>
                Pendapatan
            </h3>

            <div class="text-4xl font-bold mt-3">

                Rp 6.000.000

            </div>

        </div>


        <div class="bg-white rounded-3xl p-8">

            <h3>

                Sewa Aktif

            </h3>

            <div class="text-4xl font-bold">

                10

            </div>

        </div>


        <div class="bg-white rounded-3xl p-8">

            <h3>

                Denda

            </h3>

            <div class="text-4xl font-bold">

                Rp 500.000

            </div>

        </div>


        <div class="bg-white rounded-3xl p-8">

            <h3>

                Stok

            </h3>

            <div class="text-4xl font-bold">

                35

            </div>

        </div>

    </div>


    <div class="bg-white rounded-3xl p-8 mt-10">

        <h2 class="text-2xl font-bold mb-8">

            Aktivitas Sewa

        </h2>


        <table class="w-full">

            <tr>

                <th>Klien</th>

                <th>Alat</th>

                <th>Status</th>

            </tr>


            @for ($i = 1; $i <= 5; $i++)
                <tr>

                    <td class="py-5">

                        Budi Santoso

                    </td>

                    <td>

                        Sony A7 IV

                    </td>

                    <td>

                        <span class="bg-green-200
px-5
py-2
rounded-full">

                            Aktif

                        </span>

                    </td>

                </tr>
            @endfor


        </table>

    </div>
@endsection
