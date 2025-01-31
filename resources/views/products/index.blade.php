<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-600">
                        <thead>
                            <tr>
                                <th class="border border-gray-600 px-4 py-2 text-left text-gray-400">#</th>
                                <th class="border border-gray-600 px-4 py-2 text-left text-gray-400">Name</th>
                                <th class="border border-gray-600 px-4 py-2 text-left text-gray-400">Description</th>
                                <th class="border border-gray-600 px-4 py-2 text-left text-gray-400">Price</th>
                                <th class="border border-gray-600 px-4 py-2 text-left text-gray-400">Image</th>                            </tr>
                        </thead>
                        <tbody class="text-gray-300">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="border border-gray-600 px-4 py-2">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="border border-gray-600 px-4 py-2">
                                        {{ $product->name }}
                                    </td>
                                    <td class="border border-gray-600 px-4 py-2">
                                        {{ Str::limit($product->description, 100) }}
                                    </td>
                                    <td class="border border-gray-600 px-4 py-2">
                                        ${{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="border border-gray-600 px-4 py-2">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 object-cover">
                                        @else
                                            <span class="text-gray-400">No image</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border border-gray-600 px-4 py-2 text-center text-gray-400">
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>  --}}
            <div id="product-grid"></div>

        </div>

    </div>
</x-app-layout>
<script>
$(document).ready(function() {
    console.log('test');

    var dataSource = new kendo.data.DataSource({
        transport: {
            read: {
                url: '{{ route("products.getProducts") }}',
                dataType: 'json'
            },
            create: {
                url: '{{ route("products.store") }}',
                dataType: 'json',
                type: 'POST',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            },
            update: {
                url: function (data) {
                    return '{{ route("products.update", ":id") }}'.replace(':id', data.id);
                },
                dataType: 'json',
                type: 'PUT',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            },
            destroy: {
                url: function (data) {
                    return '{{ route("products.destroy", ":id") }}'.replace(':id', data.id);
                },
                dataType: 'json',
                type: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            },
            parameterMap: function (data, type) {
                if (type === "read") {
                    return {
                        page: data.page,
                        pageSize: data.pageSize,
                        sort: data.sort,
                        filter: data.filter
                    };
                } else if (type === "create" || type === "update") {
                    return data;
                }
            }
        },
        schema: {
            model: {
                id:"id",
                fields: {
                    id:{editable: false, nullable: true},
                    name:{tpye: "string" },
                    description:{tpye: "string", validation: { required: true } },
                    price:{tpye: "number", validation: { required: true } },
                    image:{tpye: "string", validation: { required: true } },
                }
            },
            data: "data",
            total: "total"
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });

    $("#product-grid").kendoGrid({
        dataSource: dataSource,
        pageable: true,
        sortable: true,
        filterable: true,
        // search: {
        //     fields: ["name", "description", "price"]
        // },
        columns: [
            { field: "id", title: "ID" },
            { field: "name", title: "Name" },
            { field: "description", title: "Description" },
            { field: "price", title: "Price" },
            { field: "image", title: "Image" },
            {
                command: [
                    {
                        name: "edit",
                        text: "Edit",
                        className: "bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 text-center rounded"
                    },
                    {
                        name: "destroy",
                        text: "Delete",
                        className: "bg-red-500 hover:bg-red-700 text-white font-bold px-4 py-2 text-center rounded"
                    }
                ],
                title: "Actions",
                width: 250,
            }
        ],
        toolbar: [
            "create",
            "search",
            {
                name: "excel",
                text: "Export to Excel",
                className: "bg-green-500 hover:bg-green-700 text-white font-bold px-4 py-2 text-center rounded"
            },
            {
                name: "pdf",
                text: "Export to PDF",
                className: "bg-yellow-500 hover:bg-yellow-700 text-white font-bold px-4 py-2 text-center rounded"
            }
        ],
        editable: {
            mode: "popup"
        },
        excel: {
            fileName: "Products.xlsx",
            filterable: true,
            allPages: true
        },
        pdf: {
            fileName: "Products.pdf",
            allPages: true,
            paperSize: "A4",
            margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
            landscape: true,
            repeatHeaders: true,
            template: $("#page-template").html(),
            scale: 0.8
        }
    });

});</script>
