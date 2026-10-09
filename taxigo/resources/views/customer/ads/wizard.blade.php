@extends('customer.layouts.app')

@section('title', 'Create an Ad — Seema Cabs Goa')
@php($withNav = false)

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@section('content')
<div class="pt-4 pb-10" x-data="adWizard()" x-init="init()">
    @include('customer.components.topbar', ['title' => $campaign ? 'Ad ' . $campaign['reference'] : 'Create an Ad', 'back' => route('customer.advertise')])

    <div class="px-4 space-y-4">
        {{-- Progress --}}
        <div class="flex items-center gap-1.5">
            <template x-for="(s, i) in steps" :key="s.key">
                <button type="button" class="flex-1 h-1.5 rounded-full transition-colors"
                        :class="i <= stepIndex ? 'bg-gold' : 'bg-black/10'"
                        @click="i < stepIndex && go(i)" :aria-label="s.label"></button>
            </template>
        </div>
        <p class="text-xs font-semibold uppercase tracking-wider text-muted" x-text="'Step ' + (stepIndex + 1) + ' of ' + steps.length + ' · ' + steps[stepIndex].label"></p>

        <template x-if="campaign && campaign.status === 'changes_requested'">
            <div class="card border-amber-300 bg-amber-50">
                <p class="font-semibold text-ink text-sm mb-1">Changes requested</p>
                <p class="text-sm text-ink" x-text="(campaign.reasons || []).join(', ')"></p>
                <p class="text-sm text-muted mt-1" x-show="campaign.review_note" x-text="campaign.review_note"></p>
                <p class="text-xs text-muted mt-2">Update the image or link and resubmit. Dates and placements stay as paid.</p>
            </div>
        </template>

        <div x-show="error" x-transition class="rounded-2xl bg-red-50 border border-red-200 text-danger text-sm px-4 py-3" x-text="error"></div>

        <template x-if="agency && !campaign">
            <div class="card space-y-2">
                <p class="field-label">Agency: <span x-text="agency.name"></span> — booking for</p>
                <div class="flex gap-2">
                    <select class="field-input !py-2.5" @change="switchClient($event.target.value)">
                        <template x-for="c in agency.clients" :key="c.id">
                            <option :value="c.id" :selected="c.id == agency.current && !newClient" x-text="c.business_name"></option>
                        </template>
                        <option value="new" :selected="newClient">+ New client</option>
                    </select>
                </div>
            </div>
        </template>

        {{-- STEP: Business --}}
        <section x-show="step === 'business'" class="space-y-3">
            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">Your business</h2>
                <div>
                    <label class="field-label">Business name</label>
                    <input class="field-input" x-model="profile.business_name" maxlength="120" placeholder="e.g. Baga Beach Shack">
                </div>
                <div>
                    <label class="field-label">Category</label>
                    <select class="field-input" x-model.number="profile.category_id">
                        <option value="">Choose a category</option>
                        <template x-for="c in categories" :key="c.id">
                            <option :value="c.id" x-text="c.name + (c.tier === 'premium' ? ' (Premium rates)' : '')" :selected="c.id == profile.category_id"></option>
                        </template>
                    </select>
                    <p class="text-xs text-muted mt-1.5" x-show="category && category.rules" x-text="category && category.rules"></p>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <div><label class="field-label">Contact person</label><input class="field-input" x-model="profile.contact_name" maxlength="100"></div>
                    <div><label class="field-label">Phone</label><input class="field-input" x-model="profile.phone" inputmode="tel" maxlength="14"></div>
                    <div><label class="field-label">Email (for invoices)</label><input class="field-input" type="email" x-model="profile.email" maxlength="150"></div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" x-model="wantsGst" class="rounded"> I want a GST invoice (business GSTIN)
                </label>
                <template x-if="wantsGst">
                    <div class="space-y-3">
                        <div><label class="field-label">GSTIN</label><input class="field-input uppercase" x-model="profile.gstin" maxlength="15"></div>
                        <div><label class="field-label">Registered business name</label><input class="field-input" x-model="profile.legal_name" maxlength="150"></div>
                        <div><label class="field-label">Billing address</label><textarea class="field-input rounded-2xl" rows="2" x-model="profile.billing_address" maxlength="500"></textarea></div>
                    </div>
                </template>
            </div>

            <template x-if="hasProfile && category && category.licence_required">
                <div class="card space-y-3">
                    <h2 class="font-semibold text-ink" x-text="category.licence_label"></h2>
                    <p class="text-xs text-muted">Required for your category. It must be valid for the whole ad period. Only our review team sees it.</p>
                    <template x-for="l in profile.licences" :key="l.id">
                        <div class="text-sm flex justify-between gap-2 border-b border-black/[0.05] pb-2">
                            <span class="truncate" x-text="(l.number || l.name || l.type)"></span>
                            <span class="text-muted shrink-0" x-text="'valid to ' + l.valid_until + ' · ' + l.status"></span>
                        </div>
                    </template>
                    <div class="grid grid-cols-1 gap-2">
                        <input class="field-input" placeholder="Licence number" x-model="licence.number" maxlength="100">
                        <div><label class="field-label">Valid until</label><input class="field-input" type="date" x-model="licence.valid_until" :min="today"></div>
                        <input type="file" accept=".pdf,image/*" x-ref="licenceFile" class="text-sm">
                        <button type="button" class="btn-outline" @click="uploadLicence()" :disabled="busy">Upload licence</button>
                    </div>
                </div>
            </template>

            <button type="button" class="btn-primary w-full" @click="saveProfile()" :disabled="busy">
                <span x-text="hasProfile ? 'Save & continue' : 'Continue'"></span>
            </button>
        </section>

        {{-- STEP: Placements --}}
        <section x-show="step === 'placements'" class="space-y-3">
            <p class="text-sm text-muted">Choose where your ad appears. Prices are per day for <strong x-text="tierLabel"></strong> advertisers, before GST.</p>
            <template x-for="p in placements" :key="p.id">
                <button type="button" class="card p-4 w-full text-left flex gap-3 items-start transition-shadow"
                        :class="selected.includes(p.id) ? 'ring-2 ring-gold' : ''" @click="toggle(p.id)" :disabled="locked">
                    <div class="w-12 shrink-0">
                        <div class="w-12 rounded-md bg-black/[0.06] border border-black/10 flex items-center justify-center text-[10px] font-bold text-muted"
                             :style="'aspect-ratio:' + p.width + '/' + p.height + ';max-height:64px'" x-text="p.shape"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-ink text-sm"><span x-text="p.code"></span> · <span x-text="p.name"></span>
                            <span x-show="p.exclusive" class="ml-1 text-[10px] uppercase font-bold text-orange-700">Exclusive</span></p>
                        <p class="text-xs text-muted mt-0.5" x-text="p.description"></p>
                        <p class="text-sm font-bold text-ink mt-1" x-text="money(price(p)) + ' / ' + (p.unit || 'day')"></p>
                    </div>
                    <div class="w-6 h-6 rounded-full border-2 shrink-0 flex items-center justify-center"
                         :class="selected.includes(p.id) ? 'bg-gold border-gold' : 'border-black/20'">
                        <svg x-show="selected.includes(p.id)" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                </button>
            </template>
            <template x-if="hasCode('P14')">
                <div class="card space-y-2">
                    <label class="field-label">P14 — which sightseeing package is your business on?</label>
                    <select class="field-input" x-model.number="packageId" :disabled="locked">
                        <option value="">Choose a package</option>
                        <template x-for="pk in packages" :key="pk.id"><option :value="pk.id" x-text="pk.title" :selected="pk.id == packageId"></option></template>
                    </select>
                </div>
            </template>
            <template x-if="hasCode('P18')">
                <div class="card space-y-2">
                    <label class="field-label">P18 — how many cabs should carry your QR card?</label>
                    <input type="number" class="field-input" min="1" max="200" x-model.number="cabs" :disabled="locked" @change="loadAvailability(); refreshQuote()">
                    <p class="text-xs text-muted">Booked by the month (30, 60 or 90 days). We print the cards and place them in the cabs.</p>
                </div>
            </template>
            <template x-if="hasCode('P9')">
                <div class="card space-y-2">
                    <p class="field-label">P9 — website pages (priced per group)</p>
                    <template x-for="(label, key) in targetingOptions.page_groups" :key="key">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" class="rounded" :value="key" x-model="pageGroups" :disabled="locked" @change="loadAvailability(); refreshQuote()"> <span x-text="label"></span></label>
                    </template>
                </div>
            </template>
            <template x-for="b in bundleHints" :key="b.name">
                <p class="text-xs text-success" x-text="'Bundle price applied: ' + b.name + ' (' + b.codes.join(' + ') + ')'"></p>
            </template>
            <button type="button" class="btn-primary w-full" @click="next()" :disabled="!selected.length || (hasCode('P14') && !packageId) || (hasCode('P9') && !pageGroups.length)">Continue</button>
        </section>

        {{-- STEP: Dates --}}
        <section x-show="step === 'dates'" class="space-y-3">
            <div class="card space-y-3">
                <div>
                    <label class="field-label">Start date</label>
                    <input type="date" class="field-input" x-model="startDate" :min="today" :disabled="locked" @change="refreshQuote()">
                </div>
                <div>
                    <label class="field-label">Number of days (minimum <span x-text="rules.min_days"></span>)</label>
                    <input type="number" class="field-input" x-model.number="days" :min="rules.min_days" :max="rules.max_days" :disabled="locked" @change="refreshQuote()">
                    <div class="flex gap-2 mt-2" x-show="!locked">
                        <template x-for="d in (hasCode('P18') ? [30, 60, 90] : [7, 14, 30])" :key="d">
                            <button type="button" class="btn-outline !px-4 !py-2 text-sm" :class="days === d && 'ring-2 ring-gold'" @click="days = d; refreshQuote()"
                                    x-text="d + ' days' + (d >= 30 ? ' −' + rules.discount_30 + '%' : d >= 14 ? ' −' + rules.discount_14 + '%' : '')"></button>
                        </template>
                    </div>
                </div>
                <p class="text-sm text-ink" x-show="startDate && days">Runs <strong x-text="fmtDate(startDate)"></strong> to <strong x-text="fmtDate(endDate)"></strong>.</p>
            </div>

            <template x-if="conflicts.length">
                <div class="rounded-2xl bg-red-50 border border-red-200 text-sm px-4 py-3 text-danger">
                    <p class="font-semibold mb-1">Some placements are sold out on these dates:</p>
                    <template x-for="c in conflicts" :key="c.code"><p x-text="c.code + ': ' + c.dates"></p></template>
                    <p class="mt-1 text-ink" x-show="nextFree">First date everything is free: <button type="button" class="underline font-semibold" @click="startDate = nextFree; refreshQuote()" x-text="fmtDate(nextFree)"></button></p>
                </div>
            </template>

            <label class="card flex items-start gap-3 text-sm" x-show="!locked">
                <input type="checkbox" class="rounded mt-0.5" x-model="exclusive" @change="refreshQuote()">
                <span><strong>Category exclusivity</strong> (+<span x-text="rules.exclusivity_percent"></span>%) — no other
                    <span x-text="category ? category.name : 'business of your category'"></span> ad on these placements and days.</span>
            </label>

            <div class="card" x-show="quote">@include('customer.ads.partials.quote')</div>

            <p class="text-xs text-danger" x-show="hasCode('P18') && days % 30 !== 0">QR cards are booked by the month: choose 30, 60 or 90 days.</p>
            <button type="button" class="btn-primary w-full" @click="saveDraftAndNext()" :disabled="busy || conflicts.length || days < rules.min_days || !startDate || (hasCode('P18') && days % 30 !== 0)">Continue</button>
        </section>

        {{-- STEP: Targeting --}}
        <section x-show="step === 'targeting'" class="space-y-3">
            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">Who sees your ad <span class="text-muted font-normal text-sm">(optional)</span></h2>
                <p class="text-xs text-muted">Leave everything empty to show your ad to everyone. Area and trip type apply on the trip screens, where we know the booking; day and time apply everywhere.</p>
                <div>
                    <p class="field-label">Area</p>
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="(label, key) in targetingOptions.areas" :key="key">
                            <label class="btn-outline !px-3 !py-2 text-sm cursor-pointer" :class="target.areas.includes(key) && 'ring-2 ring-gold'"><input type="checkbox" class="hidden" :value="key" x-model="target.areas" :disabled="locked"><span x-text="label"></span></label>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="field-label">Trip type</p>
                    <div class="flex gap-2 flex-wrap">
                        <template x-for="(label, key) in targetingOptions.trips" :key="key">
                            <label class="btn-outline !px-3 !py-2 text-sm cursor-pointer" :class="target.trips.includes(key) && 'ring-2 ring-gold'"><input type="checkbox" class="hidden" :value="key" x-model="target.trips" :disabled="locked"><span x-text="label"></span></label>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="field-label">Days</p>
                    <div class="flex gap-1.5 flex-wrap">
                        <template x-for="d in [1,2,3,4,5,6,0]" :key="d">
                            <label class="btn-outline !px-3 !py-2 text-sm cursor-pointer" :class="target.days.includes(String(d)) && 'ring-2 ring-gold'"><input type="checkbox" class="hidden" :value="String(d)" x-model="target.days" :disabled="locked"><span x-text="targetingOptions.days[d]"></span></label>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="field-label">Time of day</p>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="time" class="field-input" x-model="target.from" :disabled="locked">
                        <input type="time" class="field-input" x-model="target.to" :disabled="locked">
                    </div>
                    <p class="text-xs text-muted mt-1">e.g. 18:00 to 02:00 for evenings and late nights. Leave empty for all day.</p>
                </div>
            </div>
            <button type="button" class="btn-primary w-full" @click="saveDraftAndNext()" :disabled="busy">Continue</button>
        </section>

        {{-- STEP: Creative --}}
        <section x-show="step === 'creative'" class="space-y-3">
            <template x-for="shape in shapes" :key="shape.key">
                <div class="card space-y-3">
                    <div class="flex items-baseline justify-between gap-2">
                        <h2 class="font-semibold text-ink">Image <span x-text="shape.shape"></span></h2>
                        <span class="text-xs text-muted" x-text="shape.width + ' × ' + shape.height + ' px'"></span>
                    </div>
                    <p class="text-xs text-muted" x-text="'Used for: ' + shape.codes.join(', ')"></p>
                    <template x-if="creatives[shape.key]">
                        <div class="relative rounded-xl overflow-hidden border border-black/10">
                            <img :src="creatives[shape.key].url" class="w-full h-auto">
                            <span class="absolute top-1.5 right-1.5 rounded bg-black/55 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Sponsored</span>
                        </div>
                    </template>
                    <div class="flex gap-2 flex-wrap">
                        <label class="btn-outline !py-2.5 text-sm cursor-pointer">
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="pick($event, shape)">
                            <span x-text="creatives[shape.key] ? 'Replace image' : 'Choose image'"></span>
                        </label>
                        <button type="button" class="btn-outline !py-2.5 text-sm" x-show="lastFile && !creatives[shape.key]" @click="openCropper(lastFile, shape)">Use the same photo</button>
                    </div>
                    <p class="text-xs text-muted">JPEG, PNG or WebP up to 10 MB. Keep text large and away from the edges — a "Sponsored" tag is added at the top-right.</p>
                </div>
            </template>

            {{-- Where it appears --}}
            <div class="card" x-show="Object.keys(creatives).length">
                <h2 class="font-semibold text-ink mb-3">Preview</h2>
                <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1">
                    <template x-for="p in selectedPlacements" :key="p.id">
                        <div class="shrink-0 text-center">
                            <div class="relative w-32 rounded-[18px] border-4 border-ink bg-cream overflow-hidden" style="aspect-ratio: 9/19">
                                <div class="absolute inset-x-2 top-2 h-2 rounded bg-black/10"></div>
                                <div class="absolute inset-x-2 top-6 h-10 rounded bg-black/[0.06]" x-show="p.code !== 'P13'"></div>
                                <div class="absolute inset-x-2 bottom-3 h-6 rounded bg-black/[0.06]" x-show="p.code !== 'P13'"></div>
                                <template x-if="creatives[p.key]">
                                    <img :src="creatives[p.key].url" class="absolute shadow" :style="mockStyle(p)">
                                </template>
                            </div>
                            <p class="text-[11px] text-muted mt-1" x-text="p.code"></p>
                        </div>
                    </template>
                </div>
            </div>

            <template x-if="hasCode('P11')">
                <div class="card space-y-2">
                    <p class="field-label">Sponsored push (P11)</p>
                    <input class="field-input" x-model="headline" maxlength="40" placeholder="Title, e.g. 20% off at Club X tonight">
                    <textarea class="field-input rounded-2xl" rows="2" x-model="pushBody" maxlength="120" placeholder="Message (up to 120 characters)"></textarea>
                    <p class="text-xs text-muted">Sent once on the start date, between 10 am and 8 pm, to customers who chose to get offers
                        (about <span x-text="pushAudience"></span> today; each person gets at most one sponsored push a week). Shown as "Sponsored · your title".</p>
                </div>
            </template>
            <template x-if="hasCode('P16')">
                <div class="card space-y-2">
                    <label class="field-label">App-open message (P16, up to 40 characters)</label>
                    <input class="field-input" x-model="headline" maxlength="40" placeholder="e.g. Live music tonight at Club X">
                    <p class="text-xs text-muted">Shown as "Presented by" with your square logo when the app opens.</p>
                </div>
            </template>

            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">When people tap your ad</h2>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="t in [['website','Website'],['whatsapp','WhatsApp'],['call','Call']]" :key="t[0]">
                        <button type="button" class="btn-outline !px-2 !py-2.5 text-sm" :class="landingType === t[0] && 'ring-2 ring-gold'" @click="landingType = t[0]" x-text="t[1]"></button>
                    </template>
                </div>
                <input class="field-input" x-model="landingValue" :inputmode="landingType === 'website' ? 'url' : 'tel'"
                       :placeholder="landingType === 'website' ? 'https://yourbusiness.com/offer' : '10-digit mobile number'" maxlength="500">
                <p class="text-xs text-muted" x-show="landingType === 'whatsapp'">Opens WhatsApp with "Hi, I saw your ad on Seema Cabs Goa".</p>
            </div>

            <button type="button" class="btn-primary w-full" @click="saveDraftAndNext()" :disabled="busy || !allCreatives || !landingValue || ((hasCode('P16') || hasCode('P11')) && !headline) || (hasCode('P11') && !pushBody)">Continue</button>
        </section>

        {{-- STEP: Checklist --}}
        <section x-show="step === 'checklist'" class="space-y-3">
            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">Before you submit</h2>
                <p class="text-xs text-muted">Every ad is checked by our team. Tick each item to confirm.</p>
                @foreach($checklist as $key => $text)
                    <label class="flex items-start gap-3 text-sm text-ink">
                        <input type="checkbox" class="mt-0.5 rounded shrink-0" x-model="ticks.{{ $key }}">
                        <span>{{ $text }}</span>
                    </label>
                @endforeach
                <p class="text-xs text-muted pt-1">Not allowed: alcohol and tobacco products, betting and real-money games, adult content, drugs, weapons, political or religious content, competing taxi services, and misleading claims.</p>
            </div>
            <button type="button" class="btn-primary w-full" @click="campaign && campaign.status === 'changes_requested' ? resubmit() : next()" :disabled="busy || !allTicked"
                    x-text="campaign && campaign.status === 'changes_requested' ? 'Resubmit for review' : 'Continue to payment'"></button>
        </section>

        {{-- STEP: Pay --}}
        <section x-show="step === 'pay'" class="space-y-3">
            <div class="card space-y-2">
                <h2 class="font-semibold text-ink">Review &amp; pay</h2>
                <p class="text-sm text-ink"><strong x-text="selectedPlacements.map(p => p.code + ' ' + p.name).join(', ')"></strong></p>
                <p class="text-sm text-muted" x-text="fmtDate(startDate) + ' – ' + fmtDate(endDate) + ' · ' + days + ' days'"></p>
                <div class="pt-2 border-t border-black/[0.05]">@include('customer.ads.partials.quote')</div>
                <div class="pt-2 flex gap-2" x-show="!locked">
                    <input class="field-input uppercase !py-2.5" x-model="couponCode" placeholder="Coupon code" maxlength="30">
                    <button type="button" class="btn-outline !py-2.5 text-sm shrink-0" @click="applyCoupon()" :disabled="busy">Apply</button>
                </div>
                <p class="text-xs text-danger" x-show="couponError" x-text="couponError"></p>
                <p class="text-xs text-muted" x-show="quote && quote.coupon_note" x-text="quote && quote.coupon_note"></p>
                <p class="text-xs text-muted">Your slots are held for 15 minutes while you pay. If your ad isn't approved, you get a full refund automatically.</p>
            </div>

            <div class="card space-y-2" x-show="pg.razorpay_enabled && pg.cashfree_enabled">
                <p class="field-label">Pay with</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="btn-outline !py-2.5 text-sm" :class="gateway === 'razorpay' && 'ring-2 ring-gold'" @click="gateway = 'razorpay'">Razorpay</button>
                    <button type="button" class="btn-outline !py-2.5 text-sm" :class="gateway === 'cashfree' && 'ring-2 ring-gold'" @click="gateway = 'cashfree'">Cashfree</button>
                </div>
            </div>

            <button type="button" class="btn-primary w-full" @click="pay()" :disabled="busy">
                <span x-text="busy ? 'Please wait…' : 'Pay ' + (quote ? money(quote.total_amount) : '')"></span>
            </button>
            <button type="button" class="w-full text-sm text-muted underline" @click="discard()" x-show="campaign && !campaign.paid">Cancel this ad</button>
        </section>
    </div>

    {{-- Cropper modal --}}
    <div x-show="crop.open" style="display:none" class="fixed inset-0 z-50 bg-black/80 flex flex-col">
        <div class="flex-1 min-h-0 p-3"><img x-ref="cropImg" class="block max-w-full" alt=""></div>
        <div class="bg-white p-4 space-y-3 rounded-t-3xl">
            <p class="text-sm text-ink">Drag and zoom to frame your ad (<span x-text="crop.shape && crop.shape.shape"></span>).</p>
            <p class="text-xs text-danger" x-show="crop.warning" x-text="crop.warning"></p>
            <div class="grid grid-cols-4 gap-2">
                <button type="button" class="btn-outline !px-2 !py-2.5 text-sm" @click="cropper && cropper.rotate(-90)">⟲</button>
                <button type="button" class="btn-outline !px-2 !py-2.5 text-sm" @click="cropper && cropper.rotate(90)">⟳</button>
                <button type="button" class="btn-outline !px-2 !py-2.5 text-sm" @click="closeCropper()">Cancel</button>
                <button type="button" class="btn-primary !px-2 !py-2.5 text-sm" @click="useCrop()" :disabled="busy" x-text="busy ? '…' : 'Use'"></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
<script>
function adWizard() {
    const routes = {
        profile: @js(route('customer.actions.ads.profile')),
        licences: @js(route('customer.actions.ads.licences')),
        availability: @js(route('customer.actions.ads.availability')),
        quote: @js(route('customer.actions.ads.quote')),
        store: @js(route('customer.actions.ads.campaigns.store')),
        base: @js(url('/app/actions/ads/campaigns')),
        settings: @js(route('customer.actions.settings')),
    };
    const today = @js(now('Asia/Kolkata')->toDateString());

    return {
        profile: @js($profile),
        hasProfile: @js($hasProfile),
        categories: @js($categories),
        placements: @js($placements),
        rules: @js($rules),
        campaign: @js($campaign),
        targetingOptions: @js($targetingOptions),
        packages: @js($packages),
        bundles: @js($bundles),
        steps: [
            { key: 'business', label: 'Business' }, { key: 'placements', label: 'Placements' }, { key: 'dates', label: 'Dates' },
            { key: 'targeting', label: 'Audience' }, { key: 'creative', label: 'Image & link' }, { key: 'checklist', label: 'Checklist' }, { key: 'pay', label: 'Pay' },
        ],
        target: { areas: [], trips: [], days: [], from: '', to: '' },
        packageId: '', pageGroups: [], headline: '', exclusive: false, couponCode: '', couponError: '',
        cabs: 1, pushBody: '',
        agency: @js($agency),
        newClient: @js(request()->boolean('new_client')),
        pushAudience: @js($pushAudience),
        step: 'business',
        today,
        selected: [], startDate: '', days: 7,
        soldOut: {}, quote: null,
        landingType: 'website', landingValue: '',
        creatives: {}, lastFile: null,
        ticks: {},
        wantsGst: false,
        licence: { number: '', valid_until: '' },
        crop: { open: false, shape: null, file: null, warning: '' },
        cropper: null,
        pg: { razorpay_enabled: true, cashfree_enabled: false, primary: 'both', cashfree_mode: 'sandbox' },
        gateway: 'razorpay', paymentKey: '',
        busy: false, error: '',

        init() {
            this.wantsGst = !!this.profile.gstin;
            this.startDate = this.addDays(today, 1);
            if (this.campaign) {
                this.selected = [...this.campaign.placements];
                this.startDate = this.campaign.start_date || this.startDate;
                this.days = this.campaign.days || this.rules.min_days;
                this.landingType = this.campaign.landing_type || 'website';
                this.landingValue = this.campaign.landing_value || '';
                this.creatives = this.campaign.creatives || {};
                this.quote = this.campaign.quote;
                if (Array.isArray(this.creatives)) this.creatives = {};
                const t = this.campaign.targeting || {};
                this.target = { areas: t.areas || [], trips: t.trips || [], days: (t.days || []).map(String), from: (t.hours || {}).from || '', to: (t.hours || {}).to || '' };
                this.packageId = t.package_id || '';
                this.pageGroups = t.page_groups || [];
                this.headline = this.campaign.headline || '';
                this.pushBody = this.campaign.push_body || '';
                this.cabs = t.cabs || 1;
                this.exclusive = !!this.campaign.exclusive_category;
                this.couponCode = this.campaign.coupon_code || '';
            }
            this.days = Math.max(this.days, this.rules.min_days);
            this.step = !this.hasProfile ? 'business' : (this.locked ? 'creative' : (this.campaign ? 'dates' : 'placements'));
            if (this.selected.length) this.loadAvailability();
            this.loadGateways();
        },

        get stepIndex() { return this.steps.findIndex(s => s.key === this.step); },
        get locked() { return !!(this.campaign && this.campaign.paid); },
        get category() { return this.categories.find(c => c.id == this.profile.category_id) || null; },
        get tier() { return (this.category && this.category.tier) || 'standard'; },
        get tierLabel() { return this.tier === 'premium' ? 'Premium' : 'Standard'; },
        get endDate() { return this.startDate ? this.addDays(this.startDate, Math.max(1, this.days) - 1) : ''; },
        get selectedPlacements() { return this.placements.filter(p => this.selected.includes(p.id)); },
        get shapes() {
            const map = {};
            this.selectedPlacements.forEach(p => {
                map[p.key] = map[p.key] || { key: p.key, shape: p.shape, width: p.width, height: p.height, codes: [] };
                map[p.key].codes.push(p.code);
            });
            return Object.values(map);
        },
        get allCreatives() { return this.shapes.every(s => this.creatives[s.key]); },
        get bundleHints() {
            const codes = this.selectedPlacements.map(p => p.code);
            return this.bundles.filter(b => b.codes.every(c => codes.includes(c)));
        },
        hasCode(code) { return this.selectedPlacements.some(p => p.code === code); },
        targetingPayload() {
            return {
                areas: this.target.areas, trips: this.target.trips, days: this.target.days,
                hours: (this.target.from && this.target.to) ? { from: this.target.from, to: this.target.to } : null,
                package_id: this.hasCode('P14') ? this.packageId : null,
                page_groups: this.hasCode('P9') ? this.pageGroups : [],
                cabs: this.hasCode('P18') ? this.cabs : null,
            };
        },
        async switchClient(id) {
            if (id === 'new') { window.location.href = @js(route('customer.ads.create')) + '?new_client=1'; return; }
            try {
                await apiFetch(@js(route('customer.actions.ads.client')), { method: 'POST', body: { id: Number(id) } });
                window.location.href = @js(route('customer.ads.create'));
            } catch (e) { this.error = e.message; }
        },
        get allTicked() { return @js(array_keys($checklist)).every(k => this.ticks[k]); },
        get conflicts() {
            if (!this.startDate || this.locked) return [];
            const end = this.endDate;
            return this.selectedPlacements.map(p => {
                const dates = (this.soldOut[p.id] || []).filter(d => d >= this.startDate && d <= end);
                return dates.length ? { code: p.code, dates: dates.slice(0, 5).map(d => this.fmtDate(d)).join(', ') + (dates.length > 5 ? ' …' : '') } : null;
            }).filter(Boolean);
        },
        get nextFree() {
            const blocked = new Set(this.selectedPlacements.flatMap(p => this.soldOut[p.id] || []));
            let start = this.addDays(today, 1);
            for (let i = 0; i < 120; i++) {
                let ok = true;
                for (let d = 0; d < this.days; d++) { if (blocked.has(this.addDays(start, d))) { ok = false; break; } }
                if (ok) return start === this.startDate ? null : start;
                start = this.addDays(start, 1);
            }
            return null;
        },

        price(p) { return this.tier === 'premium' ? p.price_premium : p.price_standard; },
        money(paise) { return '₹' + (Number(paise || 0) / 100).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        addDays(date, n) { const d = new Date(date + 'T00:00:00'); d.setDate(d.getDate() + n); return this.ymd(d); },
        ymd(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); },
        fmtDate(ymd) { if (!ymd) return ''; const d = new Date(ymd + 'T00:00:00'); return d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }); },
        mockStyle(p) {
            const pos = { P1: 'left:8%;right:8%;top:14%', P2: 'left:8%;right:8%;top:42%', P3: 'left:8%;right:8%;top:20%', P4: 'left:8%;right:8%;bottom:16%',
                P12: 'left:10%;right:10%;top:12%', P13: 'left:0;right:0;top:0;bottom:0;height:100%', P5: 'left:8%;right:8%;top:45%',
                P6: 'left:8%;right:8%;top:48%', P7: 'left:12%;right:12%;top:30%', P8: 'left:8%;right:8%;bottom:16%' };
            return (pos[p.code] || 'left:8%;right:8%;top:30%') + ';width:' + (p.code === 'P13' ? '100%' : 'auto') + ';border-radius:6px';
        },

        go(i) { this.error = ''; this.step = this.steps[i].key; window.scrollTo(0, 0); },
        next() {
            this.error = '';
            const i = this.stepIndex;
            if (this.step === 'placements' && this.hasCode('P18') && this.days % 30 !== 0) { this.days = 30; }
            if (this.step === 'placements') { this.loadAvailability(); this.refreshQuote(); }
            if (this.step === 'dates' && this.locked) { /* paid: dates fixed */ }
            this.step = this.steps[Math.min(i + 1, this.steps.length - 1)].key;
            window.scrollTo(0, 0);
        },
        toggle(id) {
            if (this.locked) return;
            this.selected = this.selected.includes(id) ? this.selected.filter(x => x !== id) : [...this.selected, id];
        },

        async saveProfile() {
            this.error = ''; this.busy = true;
            try {
                const body = { ...this.profile };
                if (!this.wantsGst) { body.gstin = ''; body.legal_name = ''; body.billing_address = ''; }
                delete body.licences; delete body.tier;
                if (this.newClient) body.new_client = 1;
                const res = await apiFetch(routes.profile, { method: 'POST', body });
                this.profile = res.data;
                const firstTime = !this.hasProfile || this.newClient;
                this.hasProfile = true;
                this.newClient = false;
                if (firstTime && this.category && this.category.licence_required) { this.busy = false; return; }
                this.step = this.locked ? 'creative' : 'placements';
                window.scrollTo(0, 0);
            } catch (e) { this.error = e.message; }
            this.busy = false;
        },
        async uploadLicence() {
            const file = this.$refs.licenceFile && this.$refs.licenceFile.files[0];
            if (!file || !this.licence.valid_until) { this.error = 'Choose the licence file and its expiry date.'; return; }
            this.error = ''; this.busy = true;
            try {
                const fd = new FormData();
                fd.append('file', file); fd.append('number', this.licence.number || ''); fd.append('valid_until', this.licence.valid_until);
                const res = await apiFetch(routes.licences, { method: 'POST', body: fd });
                this.profile = res.data;
                this.licence = { number: '', valid_until: '' };
                this.$refs.licenceFile.value = '';
            } catch (e) { this.error = e.message; }
            this.busy = false;
        },
        async loadAvailability() {
            if (!this.selected.length) return;
            const q = this.selected.map(id => 'placements[]=' + id).join('&') + (this.campaign ? '&campaign=' + this.campaign.id : '')
                + (this.hasCode('P9') ? this.pageGroups.map(g => '&page_groups[]=' + encodeURIComponent(g)).join('') : '')
                + (this.hasCode('P18') ? '&cabs=' + this.cabs : '');
            try { const res = await apiFetch(routes.availability + '?' + q); this.soldOut = res.data || {}; } catch (e) { /* non-critical */ }
        },
        async refreshQuote() {
            if (!this.selected.length || this.days < 1) return;
            try {
                const res = await apiFetch(routes.quote, { method: 'POST', body: {
                    placements: this.selected, days: this.days, start_date: this.startDate,
                    page_groups: this.hasCode('P9') ? this.pageGroups : [], exclusive_category: this.exclusive, cabs: this.hasCode('P18') ? this.cabs : 1,
                    coupon_code: this.couponCode || null, campaign: this.campaign ? this.campaign.id : null,
                } });
                this.quote = res.data;
                this.couponError = res.data.coupon_error || '';
            } catch (e) { /* shown at checkout */ }
        },
        async saveDraft() {
            const body = {
                placements: this.selected, start_date: this.startDate, days: this.days, landing_type: this.landingType, landing_value: this.landingValue,
                targeting: this.targetingPayload(), headline: this.headline, push_body: this.pushBody, exclusive_category: this.exclusive, coupon_code: this.couponError ? null : (this.couponCode || null),
            };
            const url = this.campaign ? routes.base + '/' + this.campaign.id : routes.store;
            const res = await apiFetch(url, { method: 'POST', body });
            this.campaign = res.data;
            this.quote = res.data.quote;
            const creatives = res.data.creatives || {};
            this.creatives = Array.isArray(creatives) ? {} : creatives;
        },
        async saveDraftAndNext() {
            this.error = ''; this.busy = true;
            try { await this.saveDraft(); this.next(); } catch (e) { this.error = e.message; }
            this.busy = false;
        },

        // ---- Cropper
        pick(event, shape) {
            const file = event.target.files[0];
            event.target.value = '';
            if (!file) return;
            if (file.size > 10 * 1024 * 1024) { this.error = 'The image is larger than 10 MB.'; return; }
            this.lastFile = file;
            this.openCropper(file, shape);
        },
        openCropper(file, shape) {
            this.error = '';
            this.crop = { open: true, shape, file, warning: '' };
            const img = this.$refs.cropImg;
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
            img.onload = () => {
                if (img.naturalWidth < shape.width / 2 || img.naturalHeight < shape.height / 2) {
                    this.crop.warning = 'This image is small for this placement and may look blurry. Best size: ' + shape.width + ' × ' + shape.height + ' px.';
                }
                this.cropper = new Cropper(img, { aspectRatio: shape.width / shape.height, viewMode: 1, autoCropArea: 1, dragMode: 'move', background: false, responsive: true });
            };
            img.src = URL.createObjectURL(file);
        },
        closeCropper() {
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
            this.crop.open = false;
        },
        async useCrop() {
            if (!this.cropper || !this.campaign) return;
            const data = this.cropper.getData(true);
            this.busy = true; this.error = '';
            try {
                const fd = new FormData();
                fd.append('shape', this.crop.shape.key);
                fd.append('file', this.crop.file);
                fd.append('crop', JSON.stringify({ x: data.x, y: data.y, width: data.width, height: data.height, rotate: data.rotate || 0 }));
                const res = await apiFetch(routes.base + '/' + this.campaign.id + '/creatives', { method: 'POST', body: fd });
                this.campaign = res.data;
                this.creatives = Array.isArray(res.data.creatives) ? {} : res.data.creatives;
                this.closeCropper();
            } catch (e) { this.crop.warning = e.message; }
            this.busy = false;
        },

        // ---- Submit & pay
        async loadGateways() {
            try {
                const res = await apiFetch(routes.settings);
                if (res && res.data) {
                    this.pg.razorpay_enabled = res.data.razorpay_enabled;
                    this.pg.cashfree_enabled = res.data.cashfree_enabled;
                    this.pg.primary = res.data.primary_payment_gateway || 'both';
                    this.pg.cashfree_mode = res.data.cashfree_mode || 'sandbox';
                    this.paymentKey = res.data.payment_key || '';
                    this.gateway = (this.pg.cashfree_enabled && (!this.pg.razorpay_enabled || this.pg.primary === 'cashfree')) ? 'cashfree' : 'razorpay';
                }
            } catch (e) { /* defaults */ }
        },
        async applyCoupon() {
            this.busy = true; this.error = ''; this.couponError = '';
            try {
                await this.refreshQuote();
                if (!this.couponError) await this.saveDraft();
            } catch (e) { this.couponError = e.message; }
            this.busy = false;
        },
        async resubmit() {
            this.busy = true; this.error = '';
            try {
                await this.saveDraft();
                const res = await apiFetch(routes.base + '/' + this.campaign.id + '/resubmit', { method: 'POST', body: { checklist: this.ticks } });
                window.location.href = res.data.redirect;
            } catch (e) { this.error = e.message; this.busy = false; }
        },
        async discard() {
            if (!confirm('Cancel this ad? Nothing has been charged.')) return;
            try {
                const res = await apiFetch(routes.base + '/' + this.campaign.id + '/discard', { method: 'POST', body: {} });
                window.location.href = res.data.redirect;
            } catch (e) { this.error = e.message; }
        },
        async confirmPayment(reference) {
            const res = await apiFetch(routes.base + '/' + this.campaign.id + '/confirm', { method: 'POST', body: { gateway: this.gateway, reference } });
            window.location.href = res.data.redirect;
        },
        async pay() {
            this.busy = true; this.error = '';
            try {
                if (!this.locked) await this.saveDraft();
                const res = await apiFetch(routes.base + '/' + this.campaign.id + '/checkout', { method: 'POST', body: { gateway: this.gateway, checklist: this.ticks } });
                const order = res.data;
                if (this.gateway === 'cashfree') {
                    const cashfree = Cashfree({ mode: order.mode || this.pg.cashfree_mode });
                    cashfree.checkout({ paymentSessionId: order.payment_session_id, redirectTarget: '_modal' }).then(async (result) => {
                        if (result.error) { this.busy = false; this.error = result.error.message || 'Payment was cancelled.'; return; }
                        if (result.paymentDetails) {
                            try { await this.confirmPayment(order.order_id); } catch (e) { this.busy = false; this.error = e.message; }
                        }
                    });
                } else {
                    const rzp = new Razorpay({
                        key: this.paymentKey, amount: order.amount, currency: order.currency, order_id: order.order_id,
                        name: 'Seema Holidays', description: 'Advertising ' + order.reference,
                        prefill: { name: this.profile.contact_name, email: this.profile.email, contact: this.profile.phone },
                        theme: { color: '#FEDC33' },
                        handler: async (response) => {
                            try { await this.confirmPayment(response.razorpay_payment_id); } catch (e) { this.busy = false; this.error = e.message; }
                        },
                        modal: { ondismiss: () => { this.busy = false; } },
                    });
                    rzp.on('payment.failed', () => { this.busy = false; this.error = 'Payment failed. Please try again.'; });
                    rzp.open();
                }
            } catch (e) { this.error = e.message; this.busy = false; }
        },
    };
}
</script>
@endpush
