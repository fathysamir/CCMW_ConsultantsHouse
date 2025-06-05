@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - EPS')
@section('content')
    <style>
        #btn-outline-primary {
            color: blue;
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */

        }

        .custom-context-menu {
            display: none;
            position: absolute;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 8px 0;
            width: 180px;
            list-style: none;
            z-index: 1000;
        }

        /* Context Menu Items */
        .custom-context-menu li {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s ease-in-out;
        }

        /* Hover effect */
        .custom-context-menu li:hover {
            background: #f5f5f5;
        }

        /* Menu Icons */
        .custom-context-menu li i {
            font-size: 16px;
            color: #007bff;
            margin-bottom: 5px;
            margin-right: 5px;
        }

        /* Remove default link styles */
        .custom-context-menu li a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            width: 100%;

        }

        .custom-context-menu li a:hover {
            text-decoration: none;
        }
    </style>
    <style>
        #epsTree {
            list-style-type: none;
            padding-left: 20px;
        }

        #epsTree li {
            margin: 5px 0;
            cursor: pointer;
        }

        #epsTree ul {
            padding-left: 20px;
            border-left: 1px solid #ccc;
        }

        .category-name {
            font-weight: bold;
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $account->name }} - EPS</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('create_eps', $Account_Permissions ?? []))
            <div class="col-auto">
                <button type="button" data-toggle="modal" data-target="#createEPSModal"
                    class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create EPS</button>
            </div>
        @endif
    </div>
    <!-- Create EPS Modal -->
    <div class="modal fade" id="createEPSModal" tabindex="-1" role="dialog" aria-labelledby="createEPSModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEPSModalLabel">Create EPS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createEPSForm"method="post" action="{{ route('store_EPS') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="number" class="form-control" hidden name="accountID" value="{{ $account->id }}">

                        <div class="form-group">
                            <label for="epsName">EPS Name</label>
                            <input type="text" class="form-control" required id="epsName" name="epsName">
                        </div>
                        <div class="form-group">
                            <ul id="epsTree">
                                @foreach ($EPS as $category)
                                    @include('account_dashboard.partials.category_tree', [
                                        'category' => $category,
                                    ])
                                @endforeach
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="createEPSForm" class="btn btn-primary" id="saveEPS">Save EPS</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createEPSModal2" tabindex="-1" role="dialog" aria-labelledby="createEPSModal2Label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEPSModalLabel">Create EPS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createEPSForm2"method="post" action="{{ route('store_EPS') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="number" class="form-control" hidden name="accountID" value="{{ $account->id }}">
                        <input type="number" class="form-control" id="selected_category_input" hidden
                            name="selected_category">

                        <div class="form-group">
                            <label for="epsName">EPS Name</label>
                            <input type="text" class="form-control" required id="epsName" name="epsName" required>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="createEPSForm2" class="btn btn-primary" id="saveEPS">Save EPS</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createEPSModal3" tabindex="-1" role="dialog" aria-labelledby="createEPSModal3Label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEPSModalLabel">Update EPS Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createEPSForm3"method="post" action="{{ route('rename_EPS') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="number" class="form-control" id="category_id" hidden name="category_id">

                        <div class="form-group">
                            <label for="epsName">EPS Name</label>
                            <input type="text" class="form-control" required id="Name" name="Name" required>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="createEPSForm3" class="btn btn-primary" id="saveEPS">Save EPS</button>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div id="errorAlert" class="alert alert-danger"
            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div id="successAlert"
            class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        @foreach ($EPS as $category)
            <div class="col-md-12" id="EPS_{{ $category->id }}">
                <div class="card shadow mb-4"style="border-radius:15px;margin-bottom: 0.5rem !important;">
                    <div
                        class="card-body"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);padding-top:0.1rem;padding-bottom:0.1rem;">
                        <div class="card-text my-2"
                            onclick="getEPS('{{ $category->id }}',this);"style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;"
                            @if (!in_array($category->name, ['Archive', 'Recycle Bin'])) oncontextmenu="showContextMenu(event, '{{ $category->id }}','{{ $category->name }}')" @endif>
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ asset('dashboard/assets/images/file.png') }}"
                                        width="25" style="border-radius: 5px; margin-bottom: 5px;">
                                    <strong class="card-title my-0">{{ $category->name }}</strong>
                                </div>
                                <span class="fe fe-24 fe-chevron-right"></span>
                            </div>
                        </div>

                    </div> <!-- ./card-text -->

                </div>
            </div>
        @endforeach
        <!-- Context Menu -->
        <ul id="contextMenu" class="custom-context-menu">
            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('create_eps', $Account_Permissions ?? []))
                <li><a href="#" id="createESP"><i class="fe fe-plus-circle"></i> Create ESP</a></li>
            @endif
            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('edit_eps', $Account_Permissions ?? []))
                <li><a href="#" id="renameESP"><i class="fe fe-edit"></i> Rename ESP</a></li>
            @endif
            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('delete_eps', $Account_Permissions ?? []))
                <li><a href="#" id="deleteESP"><i class="fe fe-trash"></i> Delete ESP</a></li>
            @endif
            <li><a href="#" id="moveUp"><i class="fe fe-24 fe-arrow-up"></i> Move Up</a></li>
            <li><a href="#" id="moveDown"><i class="fe fe-24 fe-arrow-down"></i> Move Down</a></li>
        </ul>
        <div class="col-md-9">
        </div> <!-- .col -->
    </div>

@endsection
@push('scripts')
    <!-- jQuery (Must be before Bootstrap JS) -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Initialize Dropdowns Manually (If Needed) -->
    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("#epsTree li").forEach(li => {
                li.addEventListener("dblclick", function(event) {
                    let sublist = this.querySelector("ul");
                    if (sublist) {
                        sublist.style.display = sublist.style.display === "none" ? "block" : "none";
                    }
                    event.stopPropagation(); // Prevent event bubbling
                });
            });
            document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    document.querySelectorAll('.category-checkbox').forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds



        });
    </script>
    <script>
        // Function to show the context menu at the mouse pointer location
        function showContextMenu(event, categoryId, categoryName) {
            event.preventDefault(); // Prevent the default right-click menu

            const menu = document.getElementById('contextMenu');
            const moveUpBtn = document.getElementById('moveUp');
            const moveDownBtn = document.getElementById('moveDown');
            const currentItem = document.getElementById('EPS_' + categoryId);

            menu.style.left = `${event.pageX - 270}px`; // Set horizontal position
            menu.style.top = `${event.pageY - 75}px`; // Set vertical position
            menu.style.display = 'block'; // Show the menu

            // Optionally store the category ID in the context menu for later use
            menu.setAttribute('data-category-id', categoryId);
            menu.setAttribute('data-category-name', categoryName);
            const previousEPS = getPreviousEPSItem(currentItem);
            moveUpBtn.parentElement.style.display = previousEPS ? 'block' : 'none';
            const nextEPS = getNextEPSItem(currentItem);
            moveDownBtn.parentElement.style.display = nextEPS ? 'block' : 'none';
        }

        function getPreviousEPSItem(element) {
            let prev = element.previousElementSibling;
            while (prev) {
                if (prev.id && prev.id.includes('EPS_')) {
                    return prev;
                }
                prev = prev.previousElementSibling;
            }
            return null;
        }

        function getNextEPSItem(element) {
            let next = element.nextElementSibling;
            while (next) {
                if (next.id && next.id.includes('EPS_')) {
                    return next;
                }
                next = next.nextElementSibling;
            }
            return null;
        }


        // Hide the context menu when clicking anywhere else on the page
        document.getElementById('moveUp').addEventListener('click', function() {
            const menu = document.getElementById('contextMenu');
            const categoryId = menu.getAttribute('data-category-id');
            const currentItem = document.getElementById('EPS_' + categoryId);
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                id: categoryId,
                type: 'up'

            };
            $.ajax({
                url: '/reorder_EPS', // Replace with your actual controller route
                type: 'POST',
                data: formData,
                success: function(response) {

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
            if (currentItem && currentItem.previousElementSibling) {
                currentItem.parentNode.insertBefore(currentItem, currentItem.previousElementSibling);
            }

            menu.style.display = 'none'; // hide menu after action
        });

        document.getElementById('moveDown').addEventListener('click', function() {
            const menu2 = document.getElementById('contextMenu');
            const categoryId2 = menu2.getAttribute('data-category-id');
            const currentItem2 = document.getElementById('EPS_' + categoryId2);
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                id: categoryId2,
                type: 'down'

            };
            $.ajax({
                url: '/reorder_EPS', // Replace with your actual controller route
                type: 'POST',
                data: formData,
                success: function(response) {

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
            if (currentItem2 && currentItem2.nextElementSibling) {
                currentItem2.parentNode.insertBefore(currentItem2.nextElementSibling, currentItem2);
            }

            menu2.style.display = 'none'; // hide menu after action
        });

        // Handle 'Create ESP' action

        document.addEventListener('click', function() {
            const menu = document.getElementById('contextMenu');
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            }
        });
        document.getElementById('createESP').addEventListener('click', function() {
            const categoryId = document.getElementById('contextMenu').getAttribute('data-category-id');
            //alert(`Create ESP for Category ID: ${categoryId}`);   1
            const selectedCategoryInput = document.getElementById('selected_category_input');

            if (selectedCategoryInput) {
                selectedCategoryInput.value = categoryId;
                console.log("Category ID set:", categoryId);
            } else {
                console.error("Element with ID 'selected_category_input' not found!");
            }


            // Hide the context menu
            document.getElementById('contextMenu').style.display = 'none';

            $('#createEPSModal2').modal('show'); // Ensure it's using jQuery properly

        });
        document.getElementById('renameESP').addEventListener('click', function() {
            const categoryId = document.getElementById('contextMenu').getAttribute('data-category-id');
            const categoryName = document.getElementById('contextMenu').getAttribute('data-category-name');

            //alert(`Create ESP for Category ID: ${categoryId}`);
            const selectedCategoryIDInput = document.getElementById('category_id');

            if (selectedCategoryIDInput) {
                selectedCategoryIDInput.value = categoryId;
            }
            const selectedCategoryNameInput = document.getElementById('Name');

            if (selectedCategoryNameInput) {
                selectedCategoryNameInput.value = categoryName;
            }


            // Hide the context menu
            document.getElementById('contextMenu').style.display = 'none';

            $('#createEPSModal3').modal('show'); // Ensure it's using jQuery properly

        });
        document.getElementById('deleteESP').addEventListener('click', function() {
            const categoryId = document.getElementById('contextMenu').getAttribute('data-category-id');
            const categoryName = document.getElementById('contextMenu').getAttribute(
                'data-category-name');

            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                eps_id: categoryId

            };

            // Submit form data via AJAX
            $.ajax({
                url: '/getChildrenEPS', // Replace with your actual controller route
                type: 'GET',
                data: formData,
                success: function(response) {
                    if (response.data.length == 0) {
                        $.ajax({
                            url: '/getProjectsEPS', // Replace with your actual controller route
                            type: 'GET',
                            data: formData,
                            success: function(response) {
                                if (response.data.length == 0) {

                                    if (confirm(
                                            `Are you sure you want to remove EPS "${categoryName}"?`
                                        )) {
                                        $.ajax({
                                            url: '/deleteChildrenEPS', // Replace with your actual controller route
                                            type: 'POST',
                                            data: formData,
                                            success: function(response) {
                                                $('#EPS_' + categoryId)
                                                    .remove();
                                            },
                                            error: function(xhr, status, error) {
                                                console.error(error);
                                            }
                                        });
                                    } else {
                                        console.log("Deletion cancelled by user.");
                                    }
                                } else {
                                    alert(
                                        `Sorry, EPS "${categoryName}" cannot be removed because it contains other EPS or Projects`
                                    );

                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle the error response here
                                console.error(error);
                            }
                        });
                    } else {
                        alert(
                            `Sorry, EPS "${categoryName}" cannot be removed because it contains other EPS or Projects`
                        );

                    }
                },
                error: function(xhr, status, error) {
                    // Handle the error response here
                    console.error(error);
                }
            });
            // Implement the actual logic for deleting an ESP here
            document.getElementById('contextMenu').style.display = 'none'; // Hide the context menu
        });

        // Handle 'Delete ESP' action
    </script>
    <script>
        function getEPS(eps_id, element) {
            // Find the next sibling element with class "children"
            let nextChildren = $(element).next('.children');
            let icon = $(element).find('.fe-chevron-right, .fe-chevron-down');

            if (nextChildren.length > 0) {
                // If the "children" div exists, remove it and reset the icon
                nextChildren.remove();
                icon.removeClass('fe-chevron-down').addClass('fe-chevron-right'); // Change to right arrow
            } else {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formData = {
                    _token: csrfToken,
                    eps_id: eps_id

                };

                // Submit form data via AJAX
                $.ajax({
                    url: '/getChildrenEPS', // Replace with your actual controller route
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        if (response.data.length > 0) {
                            let div = `<div class="form-group children" style="align-items: center;">
                        <hr style="margin: 0rem 0rem 1rem 0rem;">`;

                            // Loop through each category in response.data
                            response.data.forEach(category => {
                                div += `<div class="col-md-12" id="EPS_${category.id}">
                                        <div class="card shadow mb-4" style="border-radius:15px;margin-bottom: 0.5rem !important;">
                                            <div class="card-body" style="border-radius:15px; box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); padding-top:0.1rem; padding-bottom:0.1rem;">
                                                <div class="card-text my-2" onclick="getEPS('${category.id}', this);"style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;"
                                                        oncontextmenu="showContextMenu(event, '${category.id}','${category.name}')">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <img src="${category.image ? category.image : '{{ asset('dashboard/assets/images/file.png') }}'}" width="25"
                                                                style="border-radius: 5px; margin-bottom: 5px;">
                                                            <strong class="card-title my-0">${category.name}</strong>
                                                        </div>
                                                        <span class="fe fe-24 fe-chevron-right"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            });

                            div += `</div>`; // Close the children div

                            // Append the generated HTML after the clicked element
                            $(element).after(div);
                            icon.removeClass('fe-chevron-right').addClass(
                                'fe-chevron-down'); // Change to down arrow

                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error response here
                        console.error(error);
                    }
                });

            }
        }
    </script>
@endpush
