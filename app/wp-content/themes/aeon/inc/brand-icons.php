<?php
/**
 * AEON duotone icon set — the filled, two-tone brand icons used on the service
 * cards (and anywhere else a "card" icon is picked from the dashboard).
 *
 * These are deliberately NOT the thin line glyphs in aeon_icon(): that set stays
 * as-is for UI chrome (arrows, check marks, pins). This one is drawn on a 48×48
 * grid with solid shapes in two brand tones, matching the company profile art.
 *
 * Colours come from CSS custom properties so a single rule can re-tint a whole
 * icon (e.g. the white-on-gradient treatment when a service card is hovered):
 *   --ico-a  primary tone (purple)
 *   --ico-b  accent tone  (orange)
 *
 * Shape classes: `da`/`db` fill with tone A/B, `sa`/`sb` stroke with tone A/B.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The duotone icon bodies, keyed by icon slug.
 *
 * @return array<string,string> Inner SVG markup for a 0 0 48 48 viewBox.
 */
function aeon_duo_icon_set() {
	static $set = null;
	if ( null !== $set ) {
		return $set;
	}

	$set = array(

		// Professional photography — camera body with a bold ring lens.
		'camera' => '
			<rect class="da" x="3" y="14" width="42" height="28" rx="6.5"/>
			<path class="da" d="M17.4 14l2.5-4.1a3.2 3.2 0 0 1 2.7-1.6h2.8a3.2 3.2 0 0 1 2.7 1.6L30.6 14z"/>
			<circle class="db" cx="24" cy="28" r="10"/>
			<circle class="da" cx="24" cy="28" r="5"/>
			<circle class="db" cx="37.5" cy="20" r="2.1"/>',

		// Graphic design — pencil on the diagonal with an inked tip. Drawn at 45°
		// on purpose: any upright, tapered-to-a-point shape reads as a map pin.
		'pen' => '
			<path class="da" d="M31.93 6.3a5.6 5.6 0 0 1 8.14 7.4L20.07 35.7 11.93 28.3z"/>
			<path class="db" d="M20.07 35.7 10.62 37.92 11.93 28.3z"/>',

		// Video production — clapperboard with striped slate and a play head.
		'video' => '
			<rect class="da" x="4" y="18" width="40" height="24" rx="5"/>
			<path class="da" d="M6.5 9.5h35A2.5 2.5 0 0 1 44 12v6H4v-6a2.5 2.5 0 0 1 2.5-2.5z"/>
			<path class="db" d="M13.4 9.5 8.9 18H14l4.5-8.5zM25.4 9.5 20.9 18H26l4.5-8.5zM37.4 9.5 32.9 18H38l4.5-8.5z"/>
			<path class="db" d="M20.8 25.2a1.2 1.2 0 0 1 1.8-1l7.9 4.6a1.2 1.2 0 0 1 0 2.1l-7.9 4.6a1.2 1.2 0 0 1-1.8-1z"/>',

		// Digital marketing — megaphone with broadcast waves.
		'megaphone' => '
			<path class="da" d="M29.5 9.8v28.4a2 2 0 0 1-2.9 1.8L15 34.2V13.8L26.6 8a2 2 0 0 1 2.9 1.8z"/>
			<path class="da" d="M8 16.5h7v15H8a4.5 4.5 0 0 1-4.5-4.5v-6A4.5 4.5 0 0 1 8 16.5z"/>
			<path class="da" d="M10.5 31.5h5.2l-1 8.6a2.2 2.2 0 0 1-2.2 1.9h-.4a2.2 2.2 0 0 1-2.2-2.5z"/>
			<path class="sb" stroke-width="3.2" d="M34.5 18.2c2 1.5 3.2 3.5 3.2 5.8s-1.2 4.3-3.2 5.8"/>
			<path class="sb" stroke-width="3.2" d="M39.6 13.4c3.2 2.5 5.1 6.2 5.1 10.6s-1.9 8.1-5.1 10.6"/>',

		// Social media management — rising bars under a growth arrow.
		'growth' => '
			<rect class="da" x="4" y="29" width="9.5" height="14" rx="2.6"/>
			<rect class="da" x="19.2" y="23.5" width="9.5" height="19.5" rx="2.6"/>
			<rect class="da" x="34.5" y="18" width="9.5" height="25" rx="2.6"/>
			<path class="sb" stroke-width="3.4" d="M5 21.5 16.5 11l7 6L38 5"/>
			<path class="sb" stroke-width="3.4" d="M29.5 5H38v8.5"/>',

		// Brand identity — dartboard with an arrow in the bullseye.
		'target' => '
			<circle class="sa" cx="21" cy="27" r="16.5" stroke-width="4"/>
			<circle class="sa" cx="21" cy="27" r="8.8" stroke-width="4"/>
			<circle class="db" cx="21" cy="27" r="3.6"/>
			<path class="sb" stroke-width="3.6" d="M21 27 37.5 10.5"/>
			<path class="db" d="M45.2 2.8 42.6 13.4 34.6 5.4z"/>',

		// Web design & development — globe with a highlighted equator.
		'globe' => '
			<circle class="sa" cx="24" cy="24" r="18.5" stroke-width="4"/>
			<ellipse class="sa" cx="24" cy="24" rx="8.6" ry="18.5" stroke-width="3.4"/>
			<path class="sa" stroke-width="3.2" d="M9.6 13.5h28.8M9.6 34.5h28.8"/>
			<path class="sb" stroke-width="3.6" d="M6 24h36"/>',

		// Performance & reporting — shield with a validation check.
		'shield' => '
			<path class="da" d="M24 3.5a2 2 0 0 1 .7.1l16 5.9a2 2 0 0 1 1.3 1.9v10.8c0 10.7-7 18.8-17.3 22.6a2 2 0 0 1-1.4 0C13 41 6 32.9 6 22.2V11.4a2 2 0 0 1 1.3-1.9l16-5.9a2 2 0 0 1 .7-.1z"/>
			<path class="sb" stroke-width="4.2" d="m16.5 23.8 5.4 5.4L32 18.6"/>',

		// Social platforms — app tile with a lens.
		'social' => '
			<rect class="da" x="5" y="5" width="38" height="38" rx="11"/>
			<circle class="db" cx="24" cy="24" r="9"/>
			<circle class="da" cx="24" cy="24" r="4.4"/>
			<circle class="db" cx="34.5" cy="13.5" r="2.6"/>',

		// Analytics — axis with plotted bars.
		'chart' => '
			<path class="sa" stroke-width="4" d="M7 5v36h34"/>
			<rect class="da" x="13" y="26" width="7" height="11" rx="2"/>
			<rect class="da" x="24" y="19" width="7" height="18" rx="2"/>
			<rect class="db" x="35" y="12" width="7" height="25" rx="2"/>',

		// Reporting — document with data lines.
		'report' => '
			<path class="da" d="M10 5.5h17.2L40 18.3v24.2a1.5 1.5 0 0 1-1.5 1.5h-28.5A1.5 1.5 0 0 1 8.5 42.5v-35.5A1.5 1.5 0 0 1 10 5.5z"/>
			<path class="db" d="M27.2 5.5 40 18.3h-11.3a1.5 1.5 0 0 1-1.5-1.5z"/>
			<path class="sb" stroke-width="3" d="M16 33.5v-5M23 33.5v-11M30 33.5v-7"/>',

		// Ideas / strategy — lightbulb.
		'bulb' => '
			<path class="da" d="M24 4c8.3 0 15 6.5 15 14.5 0 4.8-2.4 8.3-4.6 10.9-1.3 1.6-2.4 3-2.4 4.6v.5H16v-.5c0-1.6-1.1-3-2.4-4.6C11.4 26.8 9 23.3 9 18.5 9 10.5 15.7 4 24 4z"/>
			<path class="db" d="M17 38.5h14v2.2a3.3 3.3 0 0 1-3.3 3.3h-7.4a3.3 3.3 0 0 1-3.3-3.3z"/>',

		// Team — two figures.
		'team' => '
			<circle class="da" cx="18" cy="15" r="7.5"/>
			<path class="da" d="M4 40.5C4 32.7 10.3 26.4 18 26.4S32 32.7 32 40.5a1.5 1.5 0 0 1-1.5 1.5h-25A1.5 1.5 0 0 1 4 40.5z"/>
			<circle class="db" cx="34.5" cy="17.5" r="6"/>
			<path class="db" d="M34.5 25.6c5.8 0 10.5 4.7 10.5 10.5a1.4 1.4 0 0 1-1.4 1.4h-8.3c.4-1 .6-2 .6-3 0-3.4-1.3-6.5-3.4-8.9z"/>',

		// Large corporations — tower plus annex.
		'building' => '
			<path class="da" d="M5 43V12a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2v31z"/>
			<path class="da" d="M24 43V22h15a2 2 0 0 1 2 2v19z"/>
			<g class="db">
				<rect x="9" y="15" width="4" height="4" rx="1"/><rect x="15" y="15" width="4" height="4" rx="1"/>
				<rect x="9" y="22" width="4" height="4" rx="1"/><rect x="15" y="22" width="4" height="4" rx="1"/>
				<rect x="9" y="29" width="4" height="4" rx="1"/><rect x="15" y="29" width="4" height="4" rx="1"/>
				<rect x="28" y="27" width="4" height="4" rx="1"/><rect x="34" y="27" width="4" height="4" rx="1"/>
				<rect x="28" y="34" width="4" height="4" rx="1"/><rect x="34" y="34" width="4" height="4" rx="1"/>
			</g>',

		// Branch location marker.
		'pin' => '
			<path class="da" d="M24 4c-8.3 0-15 6.7-15 15 0 10.6 13.2 23.4 13.8 24a1.7 1.7 0 0 0 2.4 0C25.8 42.4 39 29.6 39 19c0-8.3-6.7-15-15-15z"/>
			<circle class="db" cx="24" cy="19" r="5.6"/>',

		// Years of experience — trophy.
		'trophy' => '
			<path class="da" d="M14 6h20v13a10 10 0 0 1-20 0z"/>
			<path class="sa" stroke-width="3" d="M14 10H9.5a4.5 4.5 0 0 0 0 9H14M34 10h4.5a4.5 4.5 0 0 1 0 9H34"/>
			<rect class="db" x="21" y="28" width="6" height="10"/>
			<path class="db" d="M14.5 38h19a1.5 1.5 0 0 1 1.5 1.5V42H13v-2.5A1.5 1.5 0 0 1 14.5 38z"/>',

		// Completed projects — briefcase.
		'briefcase' => '
			<path class="da" d="M17 13v-2.5A4.5 4.5 0 0 1 21.5 6h5A4.5 4.5 0 0 1 31 10.5V13h-4v-2a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v2z"/>
			<rect class="da" x="4" y="13" width="40" height="27" rx="5"/>
			<path class="db" d="M4 23h40v4.5H4z"/>
			<rect class="db" x="19.5" y="21" width="9" height="8" rx="2.5"/>',

		// Search optimisation — magnifier over a trend line.
		'seo' => '
			<circle class="sa" cx="21" cy="21" r="13" stroke-width="4.5"/>
			<path class="sb" stroke-width="5" d="M31 31 42 42"/>
			<path class="sb" stroke-width="3" d="M15.5 24.5l4-4.5 3.5 3 5.5-6.5"/>',

		// Web development — browser chrome with code brackets.
		'webdev' => '
			<rect class="da" x="4" y="8" width="40" height="32" rx="5"/>
			<path class="db" d="M4 13a5 5 0 0 1 5-5h30a5 5 0 0 1 5 5v3H4z"/>
			<circle class="da" cx="10" cy="12" r="1.5"/><circle class="da" cx="15" cy="12" r="1.5"/><circle class="da" cx="20" cy="12" r="1.5"/>
			<path class="sb" stroke-width="3.2" d="M18 22l-6 6 6 6M30 22l6 6-6 6"/>',

		// Competitor analysis — framed trend chart.
		'analytics' => '
			<rect class="sa" x="5" y="5" width="38" height="38" rx="9" stroke-width="4"/>
			<path class="sb" stroke-width="3.4" d="M13 31l7-7 5 4 10-11"/>
			<path class="sb" stroke-width="3.4" d="M28 17h7v7"/>',

		// Pull-quote mark.
		'quote' => '
			<path class="db" d="M9 8h11v11c0 7.2-4 12.6-11 15.4V29c3.6-1.9 5.6-4.8 5.9-9H9z"/>
			<path class="db" d="M28 8h11v11c0 7.2-4 12.6-11 15.4V29c3.6-1.9 5.6-4.8 5.9-9H28z"/>',

		// ------------------------------------------------------------------
		// Event badges. Geometry from two permissively-licensed icon sets so
		// the shapes match the client's reference artwork exactly:
		//   * Font Awesome Free 6.7.2 solid — CC BY 4.0 (fontawesome.com/license/free)
		//   * Material Symbols Rounded — Apache License 2.0
		// Each path keeps its source viewBox and is fitted into the 48x48 box
		// by the wrapping <g transform>, so the artwork is unaltered.
		// ------------------------------------------------------------------

		// Conference participation — lectern with a speaker (Material Symbols).
		'podium' => '
			<g class="da" transform="translate(-1.66 51.05) scale(0.0535)">
				<path d="M490-790q0 29-20.5 49.5T420-720q-13 0-24.5-4.5T374-737q-27 16-45 41t-19 56h495q14 0 23 10t7 24l-31 220q-2 11-10.5 18.5T774-360h-80l8-60q4-63-22.5-96.5T613-550H347q-40 0-66.5 33.5T258-420l8 60h-80q-11 0-19.5-7.5T156-386l-31-220q-2-14 7-24t23-10h95q1-49 28.5-89t71.5-63q1-29 21-48.5t49-19.5q29 0 49.5 20.5T490-790ZM374-150h212q11 0 19.5-8t9.5-19l28-280q2-13-7.5-23T613-490H347q-14 0-22.5 9.5T317-457l28 280q1 11 9.5 19t19.5 8Z"/>
			</g>',

		// Workshops & training — a group of three (Font Awesome "users").
		'users' => '
			<g class="da" transform="translate(2 6.4) scale(0.06875)">
				<path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0c-14.7 0-26.7-11.9-26.7-26.7z"/>
			</g>',

		// Practical advice — lightbulb throwing rays (Font Awesome "lightbulb").
		'idea' => '
			<g class="da" transform="translate(11.25 8) scale(0.06640625)">
				<path d="M272 384c9.6-31.9 29.5-59.1 49.2-86.2c0 0 0 0 0 0c5.2-7.1 10.4-14.2 15.4-21.4c19.8-28.5 31.4-63 31.4-100.3C368 78.8 289.2 0 192 0S16 78.8 16 176c0 37.3 11.6 71.9 31.4 100.3c5 7.2 10.2 14.3 15.4 21.4c0 0 0 0 0 0c19.8 27.1 39.7 54.4 49.2 86.2l160 0zM192 512c44.2 0 80-35.8 80-80l0-16-160 0 0 16c0 44.2 35.8 80 80 80zM112 176c0 8.8-7.2 16-16 16s-16-7.2-16-16c0-61.9 50.1-112 112-112c8.8 0 16 7.2 16 16s-7.2 16-16 16c-44.2 0-80 35.8-80 80z"/>
			</g>
			<path class="sa" stroke-width="3.2" d="M24 2.6v3.8M44.6 24h-3.8M3.4 24h3.8M38.6 9.4l-2.7 2.7M9.4 9.4l2.7 2.7"/>',

		// Panel talks — two conversation bubbles (Font Awesome "comments").
		'comments' => '
			<g class="da" transform="translate(2 6.4) scale(0.06875)">
				<path d="M208 352c114.9 0 208-78.8 208-176S322.9 0 208 0S0 78.8 0 176c0 38.6 14.7 74.3 39.6 103.4c-3.5 9.4-8.7 17.7-14.2 24.7c-4.8 6.2-9.7 11-13.3 14.3c-1.8 1.6-3.3 2.9-4.3 3.7c-.5 .4-.9 .7-1.1 .8l-.2 .2s0 0 0 0s0 0 0 0C1 327.2-1.4 334.4 .8 340.9S9.1 352 16 352c21.8 0 43.8-5.6 62.1-12.5c9.2-3.5 17.8-7.4 25.2-11.4C134.1 343.3 169.8 352 208 352zM448 176c0 112.3-99.1 196.9-216.5 207C255.8 457.4 336.4 512 432 512c38.2 0 73.9-8.7 104.7-23.9c7.5 4 16 7.9 25.2 11.4c18.3 6.9 40.3 12.5 62.1 12.5c6.9 0 13.1-4.5 15.2-11.1c2.1-6.6-.2-13.8-5.8-17.9c0 0 0 0 0 0s0 0 0 0l-.2-.2c-.2-.2-.6-.4-1.1-.8c-1-.8-2.5-2-4.3-3.7c-3.6-3.3-8.5-8.1-13.3-14.3c-5.5-7-10.7-15.4-14.2-24.7c24.9-29 39.6-64.7 39.6-103.4c0-92.8-84.9-168.9-192.6-175.5c.4 5.1 .6 10.3 .6 15.5z"/>
			</g>',

		// Strategic partnerships — clasped hands (Font Awesome "handshake").
		'handshake' => '
			<g class="da" transform="translate(2 6.4) scale(0.06875)">
				<path d="M323.4 85.2l-96.8 78.4c-16.1 13-19.2 36.4-7 53.1c12.9 17.8 38 21.3 55.3 7.8l99.3-77.2c7-5.4 17-4.2 22.5 2.8s4.2 17-2.8 22.5l-20.9 16.2L512 316.8 512 128l-.7 0-3.9-2.5L434.8 79c-15.3-9.8-33.2-15-51.4-15c-21.8 0-43 7.5-60 21.2zm22.8 124.4l-51.7 40.2C263 274.4 217.3 268 193.7 235.6c-22.2-30.5-16.6-73.1 12.7-96.8l83.2-67.3c-11.6-4.9-24.1-7.4-36.8-7.4C234 64 215.7 69.6 200 80l-72 48 0 224 28.2 0 91.4 83.4c19.6 17.9 49.9 16.5 67.8-3.1c5.5-6.1 9.2-13.2 11.1-20.6l17 15.6c19.5 17.9 49.9 16.6 67.8-2.9c4.5-4.9 7.8-10.6 9.9-16.5c19.4 13 45.8 10.3 62.1-7.5c17.9-19.5 16.6-49.9-2.9-67.8l-134.2-123zM16 128c-8.8 0-16 7.2-16 16L0 352c0 17.7 14.3 32 32 32l32 0c17.7 0 32-14.3 32-32l0-224-80 0zM48 320a16 16 0 1 1 0 32 16 16 0 1 1 0-32zM544 128l0 224c0 17.7 14.3 32 32 32l32 0c17.7 0 32-14.3 32-32l0-208c0-8.8-7.2-16-16-16l-80 0zm32 208a16 16 0 1 1 32 0 16 16 0 1 1 -32 0z"/>
			</g>',
	);

	// Let a child theme or a snippet register extra brand icons.
	$set = apply_filters( 'aeon_duo_icon_set', $set );

	return $set;
}

/**
 * Icon keys offered in the dashboard picker, with Arabic labels.
 *
 * @return array<string,string> key => Arabic label.
 */
function aeon_duo_icon_choices() {
	return apply_filters( 'aeon_duo_icon_choices', array(
		'camera'    => 'كاميرا / تصوير',
		'pen'       => 'قلم / تصميم جرافيكي',
		'video'     => 'كلاكيت / مونتاج',
		'megaphone' => 'مكبر صوت / تسويق',
		'growth'    => 'نمو / سوشيال ميديا',
		'target'    => 'هدف / هوية تجارية',
		'globe'     => 'كرة أرضية / مواقع',
		'shield'    => 'درع / تحليل وتقارير',
		'social'    => 'تطبيق سوشيال',
		'chart'     => 'رسم بياني',
		'report'    => 'تقرير / مستند',
		'bulb'      => 'فكرة',
		'team'      => 'فريق',
		'building'  => 'مبانٍ / شركات كبرى',
		'pin'       => 'موقع / فرع',
		'trophy'    => 'كأس / خبرة',
		'briefcase' => 'حقيبة / مشاريع',
		'seo'       => 'بحث / SEO',
		'webdev'    => 'تطوير مواقع',
		'analytics' => 'تحليل المنافسين',
		'quote'     => 'علامة اقتباس',
		'podium'    => 'منصة خطابة / مؤتمرات',
		'users'     => 'مجموعة أشخاص / ورش عمل',
		'idea'      => 'مصباح فكرة / نصائح',
		'comments'  => 'فقاعات حوار / جلسات نقاش',
		'handshake' => 'مصافحة / شراكات',
	) );
}

/**
 * Render a duotone brand icon.
 *
 * @param string $name    Icon key from aeon_duo_icon_set().
 * @param string $classes Extra CSS classes for the <svg>.
 * @return string SVG markup.
 */
function aeon_duo_icon( $name, $classes = '' ) {
	$set  = aeon_duo_icon_set();
	$key  = isset( $set[ $name ] ) ? $name : 'target';
	$attr = trim( 'aeon-duo ' . $classes );

	return '<svg class="' . esc_attr( $attr ) . '" viewBox="0 0 48 48" fill="none" '
		. 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
		. $set[ $key ]
		. '</svg>';
}

/**
 * The icon markup for a dashboard-managed card, honouring a custom override.
 *
 * Resolution order — first match wins:
 *   1. `icon_id`  — a file picked from the media library (SVG or raster).
 *   2. `icon_url` — a direct URL pasted into the term editor.
 *   3. `icon`     — a key from the built-in duotone set.
 *
 * @param array  $item    Item array with icon/icon_id/icon_url keys.
 * @param string $classes Extra CSS classes for the rendered element.
 * @return string HTML (an <img> for custom icons, otherwise inline SVG).
 */
function aeon_card_icon( $item, $classes = '' ) {
	$alt = isset( $item['name'] ) ? (string) $item['name'] : '';

	$id = isset( $item['icon_id'] ) ? (int) $item['icon_id'] : 0;
	if ( $id > 0 ) {
		$url = wp_get_attachment_url( $id );
		if ( $url ) {
			return aeon_custom_icon_img( $url, $alt, $classes );
		}
	}

	$url = isset( $item['icon_url'] ) ? trim( (string) $item['icon_url'] ) : '';
	if ( '' !== $url ) {
		return aeon_custom_icon_img( $url, $alt, $classes );
	}

	$key = isset( $item['icon'] ) ? (string) $item['icon'] : '';
	return aeon_duo_icon( $key, $classes );
}

/**
 * Read a term's three icon meta values in the shape aeon_card_icon() expects.
 *
 * @param int    $term_id  Term ID.
 * @param string $fallback Icon key when the term has none stored.
 * @return array{icon:string,icon_id:int,icon_url:string}
 */
function aeon_term_icon( $term_id, $fallback = 'target' ) {
	$icon = get_term_meta( $term_id, '_aeon_icon', true );
	return array(
		'icon'     => $icon ? $icon : $fallback,
		'icon_id'  => (int) get_term_meta( $term_id, '_aeon_iconfile', true ),
		'icon_url' => (string) get_term_meta( $term_id, '_aeon_iconurl', true ),
	);
}

/**
 * Dashboard terms for a section, else the brand defaults — both normalised to
 * {name, icon, icon_id, icon_url} so templates need only one shape.
 *
 * @param string $taxonomy Section taxonomy.
 * @param array  $defaults Items as {key: i18n string key, icon: icon key}.
 * @return array<int,array{name:string,icon:string,icon_id:int,icon_url:string}>
 */
function aeon_icon_list( $taxonomy, $defaults ) {
	$out = array();
	foreach ( aeon_section_terms( $taxonomy ) as $term ) {
		$out[] = array_merge( array( 'name' => $term->name ), aeon_term_icon( $term->term_id ) );
	}
	if ( $out ) {
		return $out;
	}
	foreach ( $defaults as $d ) {
		$out[] = array(
			'name'     => aeon_t( $d['key'] ),
			'icon'     => $d['icon'],
			'icon_id'  => 0,
			'icon_url' => '',
		);
	}
	return $out;
}

/**
 * <img> wrapper for a custom (uploaded or linked) icon.
 *
 * @param string $url     Icon file URL.
 * @param string $alt     Accessible name — empty renders it decorative.
 * @param string $classes Extra CSS classes.
 * @return string
 */
function aeon_custom_icon_img( $url, $alt = '', $classes = '' ) {
	$attr = trim( 'aeon-duo aeon-duo--custom ' . $classes );

	return sprintf(
		'<img class="%1$s" src="%2$s" alt="%3$s" loading="lazy" decoding="async" aria-hidden="%4$s">',
		esc_attr( $attr ),
		esc_url( $url ),
		esc_attr( $alt ),
		'' === $alt ? 'true' : 'false'
	);
}
