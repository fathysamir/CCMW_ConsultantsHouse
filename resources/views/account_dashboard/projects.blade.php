@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - Projects')
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


        .custom-context-menu li:hover {
            background: #f5f5f5;
        }


        .custom-context-menu li i {
            font-size: 16px;
            color: #007bff;
            margin-bottom: 5px;
            margin-right: 5px;
        }


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
        /* #epsTree {
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
                                    } */

        .category-name {
            font-weight: bold;
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $account->name }} - Projects</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('projects.create_project_view') }}"class="btn mb-2 btn-outline-primary"
                id="btn-outline-primary">Create Project</a>
        </div>
    </div>
    <!-- Create EPS Modal -->

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
                            onclick="getEPSandProject('{{ $category->id }}','{{ $category->name }}',this);"style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;">
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img id="logo" src="{{ asset('dashboard/assets/images/file.png') }}" width="25"
                                        style="border-radius: 5px; margin-bottom: 5px;">
                                    <strong class="card-title my-0">{{ $category->name }}</strong>
                                </div>
                                <span class="fe fe-24 fe-chevron-right"></span>
                            </div>
                        </div>

                    </div> <!-- ./card-text -->

                </div>
            </div>
        @endforeach
        <ul id="contextMenu" class="custom-context-menu">
            <li><a href="#" id="editProject"><i class="fe fe-edit"></i> Edit Project</a></li>
            <li><a href="#" id="archiveProject"><i class="fe fe-archive"></i> Archive Project</a></li>
            <li><a href="#" id="deleteProject"><i class="fe fe-trash"></i> Delete Project</a></li>
        </ul>
        <ul id="contextMenu2" class="custom-context-menu">
            <li><a href="#" id="restoreProject2"><i class="fe fe-edit"></i> Restore</a></li>
            <li><a href="#" id="deleteProject2"><i class="fe fe-trash"></i> Delete Project</a></li>
        </ul>
        <ul id="contextMenu3" class="custom-context-menu">
            <li><a href="#" id="restoreProject3"><i class="fe fe-edit"></i> Restore</a></li>
            <li><a href="#" id="archiveProject3"><i class="fe fe-archive"></i> Archive Project</a></li>
            <li><a href="#" id="deleteProject3"><i class="fe fe-trash"></i> Delete Project</a></li>
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
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds


        });
    </script>
    <script>
        // Function to show the context menu at the mouse pointer location
        function showContextMenu(event, projectId, projectName) {
            event.preventDefault(); // Prevent the default right-click menu

            const menu = document.getElementById('contextMenu');
            menu.style.left = `${event.pageX - 270}px`; // Set horizontal position
            menu.style.top = `${event.pageY - 75}px`; // Set vertical position
            menu.style.display = 'block'; // Show the menu

            // Optionally store the category ID in the context menu for later use
            menu.setAttribute('data-project-id', projectId);
            menu.setAttribute('data-project-name', projectName);
            const editProjectLink = document.getElementById('editProject');
            editProjectLink.href = `{{ route('account.edit_project_view', ':id') }}`.replace(':id', projectId);
        }

        function showContextMenu2(event, projectId, projectName) {
            event.preventDefault(); // Prevent the default right-click menu

            const menu2 = document.getElementById('contextMenu2');
            menu2.style.left = `${event.pageX - 270}px`; // Set horizontal position
            menu2.style.top = `${event.pageY - 75}px`; // Set vertical position
            menu2.style.display = 'block'; // Show the menu

            // Optionally store the category ID in the context menu for later use
            menu2.setAttribute('data-project-id', projectId);
            menu2.setAttribute('data-project-name', projectName);

        }

        function showContextMenu3(event, projectId, projectName) {
            event.preventDefault(); // Prevent the default right-click menu

            const menu3 = document.getElementById('contextMenu3');
            menu3.style.left = `${event.pageX - 270}px`; // Set horizontal position
            menu3.style.top = `${event.pageY - 75}px`; // Set vertical position
            menu3.style.display = 'block'; // Show the menu

            // Optionally store the category ID in the context menu for later use
            menu3.setAttribute('data-project-id', projectId);
            menu3.setAttribute('data-project-name', projectName);

        }

        // Hide the context menu when clicking anywhere else on the page


        // Handle 'Create ESP' action

        document.addEventListener('click', function() {
            const menu11 = document.getElementById('contextMenu');
            if (menu11.style.display === 'block') {
                menu11.style.display = 'none';
            }
            const menu22 = document.getElementById('contextMenu2');
            if (menu22.style.display === 'block') {
                menu22.style.display = 'none';
            }
            const menu33 = document.getElementById('contextMenu3');
            if (menu33.style.display === 'block') {
                menu33.style.display = 'none';
            }
        });

        document.getElementById('archiveProject').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu').getAttribute('data-project-name');

            document.getElementById('contextMenu').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };
            if (confirm(
                    `Are you sure you want to Archive Project "${projectName}"?`
                )) {
                $.ajax({
                    url: '/archiveProject', // Replace with your actual controller route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#project_' + projectId)
                            .remove();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                console.log("Archive Project cancelled by user.");
            }

        });
        document.getElementById('archiveProject3').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu3').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu3').getAttribute('data-project-name');

            document.getElementById('contextMenu3').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };
            if (confirm(
                    `Are you sure you want to Archive Project "${projectName}"?`
                )) {
                $.ajax({
                    url: '/archiveProject', // Replace with your actual controller route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#project_' + projectId)
                            .remove();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                console.log("Archive Project cancelled by user.");
            }

        });
        document.getElementById('deleteProject').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu').getAttribute('data-project-name');

            document.getElementById('contextMenu').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };
            if (confirm(
                    `Are you sure you want to Delete Project "${projectName}"?`
                )) {
                $.ajax({
                    url: '/deleteProject', // Replace with your actual controller route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#project_' + projectId)
                            .remove();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                console.log("Archive Project cancelled by user.");
            }

        });
        document.getElementById('deleteProject2').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu2').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu2').getAttribute('data-project-name');

            document.getElementById('contextMenu2').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };
            if (confirm(
                    `Are you sure you want to Delete Project "${projectName}"?`
                )) {
                $.ajax({
                    url: '/deleteProject', // Replace with your actual controller route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#project_' + projectId)
                            .remove();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                console.log("Deleting Project cancelled by user.");
            }

        });
        document.getElementById('deleteProject3').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu3').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu3').getAttribute('data-project-name');

            document.getElementById('contextMenu3').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };
            if (confirm(
                    `Are you sure you want to Delete Project "${projectName}"?`
                )) {
                $.ajax({
                    url: '/deleteProject', // Replace with your actual controller route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#project_' + projectId)
                            .remove();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                console.log("Deleting Project cancelled by user.");
            }

        });
        document.getElementById('restoreProject2').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu2').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu2').getAttribute('data-project-name');

            document.getElementById('contextMenu2').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };

            $.ajax({
                url: '/restoreProject', // Replace with your actual controller route
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#project_' + projectId)
                        .remove();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });


        });
        document.getElementById('restoreProject3').addEventListener('click', function() {
            const projectId = document.getElementById('contextMenu3').getAttribute('data-project-id');
            const projectName = document.getElementById('contextMenu3').getAttribute('data-project-name');

            document.getElementById('contextMenu3').style.display = 'none';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var formData = {
                _token: csrfToken,
                project_id: projectId

            };

            $.ajax({
                url: '/restoreProject', // Replace with your actual controller route
                type: 'POST',
                data: formData,
                success: function(response) {

                    $('#project_' + projectId)
                        .remove();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });


        });


        // Handle 'Delete ESP' action
    </script>
    <script>
        function getEPSandProject(eps_id, eps_name, element) {
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
                                                <div class="card-text my-2" onclick="getEPSandProject('${category.id}','${category.name}', this);"style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <img id="logo" src="${category.image ? category.image : '{{ asset('dashboard/assets/images/file.png') }}'}" width="25"
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
                            $.ajax({
                                url: '/getProjectsEPS', // Replace with your actual controller route
                                type: 'GET',
                                data: formData,
                                success: function(response) {
                                    if (response.data.length > 0) {


                                        // Loop through each category in response.data
                                        response.data.forEach(project => {
                                            let safeProjectName = project.name.replace(/"/g,
                                                '&quot;').replace(/'/g, '&#39;');
                                            div += `<div class="col-md-12" id="project_${project.id}">
                                                        <div class="card shadow mb-4" style="border-radius:15px;margin-bottom: 0.5rem !important;">
                                                            <div class="card-body" style="border-radius:15px; box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); padding-top:0.1rem; padding-bottom:0.1rem;">
                                                                <div class="card-text my-2" style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;"
                                                                    oncontextmenu="showContextMenu(event, '${project.id}', '${project.name.replace(/'/g, "\\'")}')">
                                                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                                            <img id="logo" src="${project.image ? project.image : '{{ asset('dashboard/assets/images/project_logo.jpg') }}'}" width="25"
                                                                                style="border-radius: 5px; margin-bottom: 5px;">
                                                                            <strong class="card-title my-0">${project.name} - ${project.code}</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                        });

                                        // Close the children div

                                        // Append the generated HTML after the clicked element



                                    }
                                    div += `</div>`; // Close the children div

                                    // Append the generated HTML after the clicked element
                                    $(element).after(div);
                                    document.querySelectorAll('[id^="project_"]').forEach(div => {
                                        div.addEventListener('click', function() {
                                            let projectId = this.id.replace(
                                                'project_', ''
                                            ); // Extract ID from div
                                            let switchProjectUrl =
                                                `/switch-project/${projectId}`; // Laravel route

                                            // Redirect to the switch project route
                                            window.location.href = switchProjectUrl;
                                        });
                                    });
                                },
                                error: function(xhr, status, error) {
                                    // Handle the error response here
                                    console.error(error);
                                }
                            });


                            icon.removeClass('fe-chevron-right').addClass(
                                'fe-chevron-down'); // Change to down arrow


                        } else {
                            $.ajax({
                                url: '/getProjectsEPS', // Replace with your actual controller route
                                type: 'GET',
                                data: formData,
                                success: function(response) {
                                    if (response.data.length > 0) {
                                        let div = `<div class="form-group children" style="align-items: center;">
                                            <hr style="margin: 0rem 0rem 1rem 0rem;">`;

                                        // Loop through each category in response.data
                                        response.data.forEach(project => {
                                            let safeProjectName = project.name.replace(/"/g,
                                                '&quot;').replace(/'/g, '&#39;');
                                            div +=
                                                `<div class="col-md-12" id="project_${project.id}">
                                        <div class="card shadow mb-4" style="border-radius:15px;margin-bottom: 0.5rem !important;">
                                            <div class="card-body" style="border-radius:15px; box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3); padding-top:0.1rem; padding-bottom:0.1rem;">
                                                <div class="card-text my-2" style="cursor:pointer;margin-bottom: 0.2rem !important;margin-top: 0.2rem !important;"`;
                                            if (eps_name == 'Archive') {
                                                div +=
                                                    `oncontextmenu="showContextMenu2(event, '${project.id}', '${project.name.replace(/'/g, "\\'")}')"`;
                                            } else if (eps_name == 'Recycle Bin') {
                                                div +=
                                                    `oncontextmenu="showContextMenu3(event, '${project.id}', '${project.name.replace(/'/g, "\\'")}')"`;
                                            } else {
                                                div +=
                                                    `oncontextmenu="showContextMenu(event, '${project.id}', '${project.name.replace(/'/g, "\\'")}')"`;
                                            }
                                            div += `>
                                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                        <div style="display: flex; align-items: center; gap: 10px;">
                                                            <img id="logo" src="${project.image ? project.image : '{{ asset('dashboard/assets/images/project_logo.jpg') }}'}" width="25"
                                                                style="border-radius: 5px; margin-bottom: 5px;">
                                                            <strong class="card-title my-0">${project.name} - ${project.code}</strong>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                        });

                                        // Close the children div

                                        // Append the generated HTML after the clicked element

                                        div += `</div>`; // Close the children div

                                        // Append the generated HTML after the clicked element
                                        $(element).after(div);
                                        icon.removeClass('fe-chevron-right').addClass(
                                            'fe-chevron-down'); // Change to down arrow
                                        document.querySelectorAll('[id^="project_"]').forEach(
                                            div => {
                                                div.addEventListener('click', function() {
                                                    let projectId = this.id.replace(
                                                        'project_', ''
                                                    ); // Extract ID from div
                                                    let switchProjectUrl =
                                                        `/switch-project/${projectId}`; // Laravel route

                                                    // Redirect to the switch project route
                                                    window.location.href =
                                                        switchProjectUrl;
                                                });
                                            });

                                    }

                                },
                                error: function(xhr, status, error) {
                                    // Handle the error response here
                                    console.error(error);
                                }
                            });


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
