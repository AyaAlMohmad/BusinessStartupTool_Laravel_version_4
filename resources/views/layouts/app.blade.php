<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('build/assets/image.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('build/assets/image.png') }}">

    <title>
        Valuenation
    </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/corporate-ui-dashboard.css?v=1.0.0') }}" rel="stylesheet" />
    <style>
     .fixed-plugin {
    position: fixed;
    top: 120px;
    right: 0;
    z-index: 1030;
}

[dir="rtl"] .fixed-plugin {
    right: auto;
    left: 0;
}

.fixed-plugin .card {
    position: fixed;
    top: 0;
    width: 300px;
    height: 100vh;
    transition: all 0.3s ease;
    z-index: 1030;
    overflow-y: auto;
}

/* للاتجاه LTR */
[dir="ltr"] .fixed-plugin .card {
    right: -300px;
}

[dir="ltr"] .fixed-plugin .card.show {
    right: 0;
}

/* للاتجاه RTL */
[dir="rtl"] .fixed-plugin .card {
    left: -300px;
}

[dir="rtl"] .fixed-plugin .card.show {
    left: 0;
}

.fixed-plugin-button {
    /* cursor: pointer;
    position: fixed;

    right: 10px;
    z-index: 1031;
    background: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
}

[dir="rtl"] .fixed-plugin-button {
    right: auto;
    left: 10px;
}
    </style>
</head>

<body class="g-sidenav-show {{ app()->getLocale() == 'ar' ? 'rtl' : '' }}  bg-gray-100">
    <aside
        class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-slate-900 fixed-start   {{ app()->getLocale() == 'ar' ? 'fixed-end me-4' : 'fixed-start ms-4' }}" " id="sidenav-main">
    @include('layouts.include.sidebar')
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg mx-5 px-0 shadow-none rounded" id="navbarBlur"
            navbar-scroll="true">
            @include('layouts.include.navebar')
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4 px-5   {{ app()->getLocale() == 'ar' ? 'fixed-end me-4' : 'fixed-start ms-4' }}"
            ">
        @yield('content')
        </div>
        </main>
        <div class="fixed-plugin">
            <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
                <i class="fa fa-cog py-2"> </i>
            </a>
            <div class="card shadow-lg">
                <div class="card-header pb-0 pt-3 ">
                    <div class="float-start">
                        <h5 class="mt-3 mb-0"> Booking system </h5>
                        <p>See our dashboard options.</p>
                    </div>
                    <div class="float-end mt-4">
                        <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                    <!-- End Toggle Button -->
                </div>
                <hr class="horizontal dark my-1">
                <div class="card-body pt-sm-3 pt-0 overflow-auto">
                    <!-- Sidebar Backgrounds -->
                    <div>
                        <h6 class="mb-0">Sidebar Colors</h6>
                    </div>
                    <a href="javascript:void(0)" class="switch-trigger background-color">
                        <div class="badge-colors my-2 text-start">
                            <span class="badge filter bg-gradient-primary active" data-color="primary"
                                onclick="sidebarColor(this)"></span>
                            <span class="badge filter bg-gradient-dark" data-color="dark"
                                onclick="sidebarColor(this)"></span>
                            <span class="badge filter bg-gradient-info" data-color="info"
                                onclick="sidebarColor(this)"></span>
                            <span class="badge filter bg-gradient-success" data-color="success"
                                onclick="sidebarColor(this)"></span>
                            <span class="badge filter bg-gradient-warning" data-color="warning"
                                onclick="sidebarColor(this)"></span>
                            <span class="badge filter bg-gradient-danger" data-color="danger"
                                onclick="sidebarColor(this)"></span>
                        </div>
                    </a>
                    <!-- Sidenav Type -->
                    <div class="mt-3">
                        <h6 class="mb-0">Sidenav Type</h6>
                        <p class="text-sm">Choose between 2 different sidenav types.</p>
                    </div>
                    <div class="d-flex">
                        <button class="btn bg-gradient-primary w-100 px-3 mb-2 active me-2" data-class="bg-white"
                            onclick="sidebarType(this)">White</button>
                        <button class="btn bg-gradient-primary w-100 px-3 mb-2" data-class="bg-default"
                            onclick="sidebarType(this)">Dark</button>
                    </div>
                    <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
                    <!-- Navbar Fixed -->
                    <div class="d-flex my-3">
                        <h6 class="mb-0">Navbar Fixed</h6>
                        <div class="form-check form-switch ps-0 ms-auto my-auto">
                            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed"
                                onclick="navbarFixed(this)">
                        </div>
                    </div>
                    <hr class="horizontal dark my-sm-4">
                    <div class="mt-2 mb-5 d-flex">
                        <h6 class="mb-0">Light / Dark</h6>
                        <div class="form-check form-switch ps-0 ms-auto my-auto">
                            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version"
                                onclick="darkMode(this)">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!--   Core JS Files   -->
        <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script> --}}
        <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/swiper-bundle.min.js') }}" type="text/javascript"></script>
        <script>
            if (document.getElementsByClassName('mySwiper')) {
                var swiper = new Swiper(".mySwiper", {
                    effect: "cards",
                    grabCursor: true,
                    initialSlide: 1,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });
            };


            var ctx = document.getElementById("chart-bars").getContext("2d");

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
                    datasets: [{
                            label: "Sales",
                            tension: 0.4,
                            borderWidth: 0,
                            borderSkipped: false,
                            backgroundColor: "#2ca8ff",
                            data: [450, 200, 100, 220, 500, 100, 400, 230, 500, 200],
                            maxBarThickness: 6
                        },
                        {
                            label: "Sales",
                            tension: 0.4,
                            borderWidth: 0,
                            borderSkipped: false,
                            backgroundColor: "#7c3aed",
                            data: [200, 300, 200, 420, 400, 200, 300, 430, 400, 300],
                            maxBarThickness: 6
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#1e293b',
                            bodyColor: '#1e293b',
                            borderColor: '#e9ecef',
                            borderWidth: 1,
                            usePointStyle: true
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            stacked: true,
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [4, 4],
                            },
                            ticks: {
                                beginAtZero: true,
                                padding: 10,
                                font: {
                                    size: 12,
                                    family: "Noto Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                color: "#64748B"
                            },
                        },
                        x: {
                            stacked: true,
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: "Noto Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                color: "#64748B"
                            },
                        },
                    },
                },
            });


            var ctx2 = document.getElementById("chart-line").getContext("2d");

            var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

            gradientStroke1.addColorStop(1, 'rgba(45,168,255,0.2)');
            gradientStroke1.addColorStop(0.2, 'rgba(45,168,255,0.0)');
            gradientStroke1.addColorStop(0, 'rgba(45,168,255,0)'); //blue colors

            var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

            gradientStroke2.addColorStop(1, 'rgba(119,77,211,0.4)');
            gradientStroke2.addColorStop(0.7, 'rgba(119,77,211,0.1)');
            gradientStroke2.addColorStop(0, 'rgba(119,77,211,0)'); //purple colors

            new Chart(ctx2, {
                plugins: [{
                    beforeInit(chart) {
                        const originalFit = chart.legend.fit;
                        chart.legend.fit = function fit() {
                            originalFit.bind(chart.legend)();
                            this.height += 40;
                        }
                    },
                }],
                type: "line",
                data: {
                    labels: ["Aug 18", "Aug 19", "Aug 20", "Aug 21", "Aug 22", "Aug 23", "Aug 24", "Aug 25", "Aug 26",
                        "Aug 27", "Aug 28", "Aug 29", "Aug 30", "Aug 31", "Sept 01", "Sept 02", "Sept 03", "Aug 22",
                        "Sept 04", "Sept 05", "Sept 06", "Sept 07", "Sept 08", "Sept 09"
                    ],
                    datasets: [{
                            label: "Volume",
                            tension: 0,
                            borderWidth: 2,
                            pointRadius: 3,
                            borderColor: "#2ca8ff",
                            pointBorderColor: '#2ca8ff',
                            pointBackgroundColor: '#2ca8ff',
                            backgroundColor: gradientStroke1,
                            fill: true,
                            data: [2828, 1291, 3360, 3223, 1630, 980, 2059, 3092, 1831, 1842, 1902, 1478, 1123,
                                2444, 2636, 2593, 2885, 1764, 898, 1356, 2573, 3382, 2858, 4228
                            ],
                            maxBarThickness: 6

                        },
                        {
                            label: "Trade",
                            tension: 0,
                            borderWidth: 2,
                            pointRadius: 3,
                            borderColor: "#832bf9",
                            pointBorderColor: '#832bf9',
                            pointBackgroundColor: '#832bf9',
                            backgroundColor: gradientStroke2,
                            fill: true,
                            data: [2797, 2182, 1069, 2098, 3309, 3881, 2059, 3239, 6215, 2185, 2115, 5430, 4648,
                                2444, 2161, 3018, 1153, 1068, 2192, 1152, 2129, 1396, 2067, 1215, 712, 2462,
                                1669, 2360, 2787, 861
                            ],
                            maxBarThickness: 6
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 6,
                                boxHeight: 6,
                                padding: 20,
                                pointStyle: 'circle',
                                borderRadius: 50,
                                usePointStyle: true,
                                font: {
                                    weight: 400,
                                },
                            },
                        },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#1e293b',
                            bodyColor: '#1e293b',
                            borderColor: '#e9ecef',
                            borderWidth: 1,
                            pointRadius: 2,
                            usePointStyle: true,
                            boxWidth: 8,
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [4, 4]
                            },
                            ticks: {
                                callback: function(value, index, ticks) {
                                    return parseInt(value).toLocaleString() + ' EUR';
                                },
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: {
                                    size: 12,
                                    family: "Noto Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                color: "#64748B"
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [4, 4]
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: {
                                    size: 12,
                                    family: "Noto Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                color: "#64748B"
                            }
                        },
                    },
                },
            });
        </script>
        <script>
            var win = navigator.platform.indexOf('Win') > -1;
            if (win && document.querySelector('#sidenav-scrollbar')) {
                var options = {
                    damping: '0.5'
                }
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }
        </script>
        <!-- Github buttons -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
        <!-- Control Center for Corporate UI Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="{{ asset('assets/js/corporate-ui-dashboard.min.js?v=1.0.0') }}"></script>
        @stack('sripts')
        <script>
           document.addEventListener("DOMContentLoaded", function() {
    const toggleButtons = document.querySelectorAll('.fixed-plugin-button');
    const pluginCard = document.querySelector('.fixed-plugin .card');
    const closeButton = document.querySelector('.fixed-plugin-close-button');

    // تحديد الاتجاه
    const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

    // تحديد موقع الزر حسب الاتجاه
    const button = document.querySelector('.fixed-plugin-button');
    if (isRTL) {
        button.style.right = 'auto';
        button.style.left = '10px';
    } else {
        button.style.left = 'auto';
        button.style.right = '10px';
    }

    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (isRTL) {
                pluginCard.style.left = pluginCard.classList.contains('show') ? '-300px' : '0';
            } else {
                pluginCard.style.right = pluginCard.classList.contains('show') ? '-300px' : '0';
            }
            pluginCard.classList.toggle('show');
        });
    });

    if (closeButton) {
        closeButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (isRTL) {
                pluginCard.style.left = '-300px';
            } else {
                pluginCard.style.right = '-300px';
            }
            pluginCard.classList.remove('show');
        });
    }
});
        </script>

</body>

</html>
