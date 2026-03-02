{{-- Public/ViewEvent/Partials/EventRegistrationSection.blade.php --}}
<section id="registrations" class="container py-5">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h1 class="section_head">Registration Options</h1>
            <p class="text-muted">Select a registration option below to secure your spot</p>
        </div>
    </div>

    <div class="row g-4">
        @php $publicRegistrations = $event->registrations->where('is_private', false); @endphp
        @if ($publicRegistrations->count() > 0 && $event->end_date->isPast() == false)
            @foreach ($publicRegistrations as $registration)
                @if($registration->end_date > now())
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card registration-card h-100 border-0 shadow-sm">
                            <!-- Image with better proportions -->
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                style="height: 160px; position: relative;">
                                @if ($registration->image)
                                    <img src="{{ asset('storage/' . $registration->image) }}"
                                        class="img-fluid object-fit-cover" alt="{{ $registration->name }}"
                                        style="width:100% ; height: 100%;" >
                                @else
                                    <div class="reg-title">{{ $registration->name }}</div>
                                @endif

                                {{-- @if($registration->category && $registration->category->conferences && $registration->category->conferences->count() > 0)
                                    @php
                                        $minPrice = $registration->category->conferences->min('price');
                                        $maxPrice = $registration->category->conferences->max('price');
                                    @endphp
                                    <div class="price-tag">
                                        @if($minPrice == $maxPrice)
                                            ${{ number_format($minPrice, 2) }}
                                        @else
                                            ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}
                                        @endif
                                    </div>
                                @endif --}}
                            </div>

                            <div class="card-body p-3">
                                <!-- Title -->
                                <h5 class="card-title fw-bold mb-2">{{ $registration->name }}</h5>

                                <!-- Category -->
                                @if ($registration->category)
                                    <div class="mb-2">
                                        <span class="category-badge">{{ $registration->category->name }}</span>
                                    </div>
                                @endif

                                <!-- Description - shorter to reduce card length -->
                                <p class="card-text text-muted small mb-2">
                                    {{ Str::limit($registration->description ?? '', 60) }}
                                </p>

                                <!-- Countdown Timer - more compact, side by side layout -->
                                <div class="countdown-container mb-3">
                                    <div class="countdown-label">Registration closes in:</div>
                                    <div class="countdown-timer" data-end="{{ $registration->end_date }}">
                                        <div class="timer-block">
                                            <span class="timer-value days">00</span>
                                            <span class="timer-label">d</span>
                                        </div>
                                        <div class="timer-separator">:</div>
                                        <div class="timer-block">
                                            <span class="timer-value hours">00</span>
                                            <span class="timer-label">h</span>
                                        </div>
                                        <div class="timer-separator">:</div>
                                        <div class="timer-block">
                                            <span class="timer-value minutes">00</span>
                                            <span class="timer-label">m</span>
                                        </div>
                                        <div class="timer-separator">:</div>
                                        <div class="timer-block">
                                            <span class="timer-value seconds">00</span>
                                            <span class="timer-label">s</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Register Button - at the bottom center -->
                            <div class="card-footer bg-transparent border-0 text-center pb-3">
                                <a href="{{ route('showEventRegistrationForm', ['event_id' => $event->id, 'event_slug' => $event->slug, 'registration_id' => $registration->id]) }}"
                                    class="btn btn-primary">
                                    Register Now
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            No upcoming Form Registartions available at this time.
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    No upcoming events available at this time.
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Add this CSS to your stylesheet -->
<style>
    .registration-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 12px;
        overflow: hidden;
    }

    .registration-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .btn-primary {
        background-color: #5829bc;
        border-color: #5829bc;
        color: white;
        border-radius: 6px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #4a1fa8;
        border-color: #4a1fa8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(88, 41, 188, 0.3);
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .category-badge {
        font-weight: 500;
        color: #5829bc;
        background: rgba(88, 41, 188, 0.1);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        display: inline-block;
    }

    .section_head {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .reg-title {
        text-align: center;
        font-size: 1.8rem;
        font-weight: 600;
        color: #5829bc;
    }

    .price-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #5829bc;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* Compact Countdown Timer Styles */
    .countdown-container {
        display: flex;
        flex-direction: column;
        background-color: rgba(88, 41, 188, 0.05);
        border-radius: 8px;
        padding: 8px;
    }

    .countdown-label {
        font-size: 0.75rem;
        color: #666;
        margin-bottom: 5px;
    }

    .countdown-timer {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timer-block {
        display: flex;
        align-items: baseline;
    }

    .timer-value {
        font-size: 1rem;
        font-weight: 700;
        color: #5829bc;
    }

    .timer-label {
        font-size: 0.7rem;
        color: #666;
        margin-left: 2px;
    }

    .timer-separator {
        margin: 0 2px;
        color: #5829bc;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize countdown timers
        const countdownTimers = document.querySelectorAll('.countdown-timer');

        countdownTimers.forEach(timer => {
            const endDate = new Date(timer.dataset.end).getTime();

            // Update the countdown every second
            const countdownInterval = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();

                // Calculate the time remaining
                const distance = endDate - now;

                // Calculate days, hours, minutes, and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update the timer display
                timer.querySelector('.days').textContent = days.toString().padStart(2, '0');
                timer.querySelector('.hours').textContent = hours.toString().padStart(2, '0');
                timer.querySelector('.minutes').textContent = minutes.toString().padStart(2, '0');
                timer.querySelector('.seconds').textContent = seconds.toString().padStart(2, '0');

                // If the countdown is finished, display expired message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    const container = timer.closest('.countdown-container');
                    if (container) {
                        container.innerHTML = '<div class="alert alert-warning p-1 text-center small">Registration ended</div>';
                    }
                }
            }, 1000);
        });
    });
</script>
