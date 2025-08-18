<x-default-layout>

    @section('title')
        Response Templates
    @endsection


    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-12">
            <!--begin::Card title-->
            <div class="card-title">
         


            </div>
            <!--begin::Card title-->


        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>




    @push('scripts')
        {{ $dataTable->scripts() }}



<script>
  
$(document).ready(function () {
            document.addEventListener('livewire:init', function () {
                Livewire.on('success', function () {
                    window.LaravelDataTables['response-templates-table'].ajax.reload();
                });
            });
        });
        </script>
    @endpush

</x-default-layout>
