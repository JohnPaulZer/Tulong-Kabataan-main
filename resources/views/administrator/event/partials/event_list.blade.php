  <!-- Events MANAGEMENT list -->
  <section id="eventsList" class="events-list">
      @foreach ($events as $event)
          <article class="event-item" id="event-{{ $event->event_id }}">
              <div class="event-top">
                  <div class="event-left">
                      <div class="event-thumb blue"><i class="ri-drop-line"></i></div>
                      <div>
                          <h3 class="event-title">{{ $event->title }}</h3>
                          <p class="event-meta">
                              {{ \Carbon\Carbon::parse($event->start_date)->format('F d, Y') }}
                              • {{ $event->location }}
                          </p>
                          @php

                              $now = Carbon\Carbon::now();
                              $start = Carbon\Carbon::parse($event->start_date);
                              $end = Carbon\Carbon::parse($event->end_date);

                              if ($now->lt($start)) {
                                  $status = 'Available';
                              } elseif ($now->between($start, $end)) {
                                  $status = 'Ongoing';
                              } else {
                                  $status = 'Ended';
                              }

                              $badgeClass = match ($status) {
                                  'Available' => 'status-available',
                                  'Ongoing' => 'status-ongoing',
                                  'Ended' => 'status-ended',
                              };
                          @endphp

                          <span class="status-badge {{ $badgeClass }}">
                              <i class="ri-checkbox-blank-circle-fill"></i> {{ $status }}
                          </span>


                      </div>
                  </div>

                  <div class="event-right">
                      <p class="muted">
                          Registered: <strong>{{ $event->registrations->count() }}</strong>
                      </p>
                      <button class="link-btn" data-target="participants-{{ $event->event_id }}">
                          View Participants
                      </button>
                      <button class="link-btn view-details-btn" data-event='@json($event)'>
                          View Details
                      </button>
                  </div>
              </div>

              <div id="participants-{{ $event->event_id }}" class="participants" hidden
                  data-event-id="{{ $event->event_id }}">
                  <div class="participants-header">
                      <h4>Participants ({{ $event->registrations->count() }})</h4>

                      <!-- BULK ACTION BUTTONS -->
                      <div class="bulk-action">
                          <!-- Mark All as Attended -->
                          <button onclick="markAllAttended({{ $event->event_id }})" class="btn-mark-all"
                              id="markAllBtn-{{ $event->event_id }}"
                              {{ $event->registrations->where('status', '!=', 'attended')->count() === 0 ? 'disabled' : '' }}>
                              <i class="ri-check-line"></i> Mark All as Attended
                          </button>

                          <!-- Mark All as Missing -->
                          <button onclick="markAllMissing({{ $event->event_id }})"
                              class="btn-mark-all btn-mark-missing" id="markAllMissingBtn-{{ $event->event_id }}"
                              {{ $event->registrations->where('status', '!=', 'absent')->count() === 0 ? 'disabled' : '' }}>
                              <i class="ri-close-line"></i> Mark All as Missing
                          </button>

                          <!-- Select Participants with Dropdown Menu -->
                          <div class="select-participants-dropdown" id="selectDropdown-{{ $event->event_id }}">
                              <button class="btn-select-participants" id="toggleSelectBtn-{{ $event->event_id }}"
                                  onclick="toggleSelectionMode({{ $event->event_id }})">
                                  <i class="ri-checkbox-multiple-line"></i> Select Participants
                              </button>

                              <!-- Dropdown Menu (Hidden by default) -->
                              <div class="dropdown-menu" id="dropdownMenu-{{ $event->event_id }}"
                                  style="display: none;">
                                  <button onclick="markSelectedAttended({{ $event->event_id }})" class="dropdown-item">
                                      <i class="ri-check-double-line"></i> Mark as Attended
                                  </button>
                                  <button onclick="markSelectedMissing({{ $event->event_id }})" class="dropdown-item">
                                      <i class="ri-close-line"></i> Mark as Missing
                                  </button>
                                  <div class="dropdown-divider"></div>
                                  <button onclick="cancelSelection({{ $event->event_id }})"
                                      class="dropdown-item cancel">
                                      <i class="ri-close-line"></i> Cancel Selection
                                  </button>
                              </div>
                          </div>
                      </div>

                      <!-- Selection Count Display -->
                      <div class="selection-count" id="selectionCountDisplay-{{ $event->event_id }}"
                          style="display: none;">
                          <span id="selectionCount-{{ $event->event_id }}">0 selected</span>
                      </div>
                  </div>

                  <div class="participants-list">
                      @forelse ($event->registrations as $reg)
                          <div class="participant-row" id="row-{{ $reg->registration_id }}"
                              data-status="{{ $reg->status }}">
                              <!-- CHECKBOX COLUMN - Hidden by default -->
                              <div class="p-checkbox" style="display: none;">
                                  <input type="checkbox" class="participant-checkbox"
                                      value="{{ $reg->registration_id }}" id="checkbox-{{ $reg->registration_id }}"
                                      {{ $reg->status == 'attended' ? 'disabled' : '' }}
                                      onchange="updateSelectedCount({{ $event->event_id }})">
                              </div>

                              <div class="p-left">
                                  <div
                                      class="avatar-sm {{ $reg->status == 'attended' ? 'attended' : '' }} {{ $reg->status == 'absent' ? 'missing' : '' }}">
                                      {{ strtoupper(substr($reg->first_name, 0, 1)) }}{{ strtoupper(substr($reg->last_name, 0, 1)) }}
                                  </div>
                                  <div>
                                      <div class="participant-name">{{ $reg->first_name }} {{ $reg->last_name }}
                                          @if ($reg->status == 'attended')
                                              <span class="status-badge attended"></span>
                                          @endif
                                          @if ($reg->status == 'absent')
                                              <span class="status-badge missing"></span>
                                          @endif
                                      </div>
                                      <div class="participant-email">{{ $reg->email }}</div>
                                  </div>
                              </div>

                              <div class="p-right">
                                  @if ($reg->status == 'registered')
                                      <span class="status-badge registered">Registered</span>
                                  @elseif($reg->status == 'attended')
                                      <span class="status-badge attended">Attended</span>
                                  @elseif($reg->status == 'absent')
                                      <span class="status-badge missing">Missing</span>
                                  @endif
                              </div>
                          </div>
                      @empty
                          <p class="muted">No participants yet for this event.</p>
                      @endforelse
                  </div>
              </div>
          </article>
      @endforeach
  </section>
