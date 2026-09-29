<?php
namespace LandTechExtras\Modules\Calendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RFC 5545 subset parser for VEVENT + RRULE expansion.
 *
 * @since 2.6.0
 */
class Ical_Parser {

	const MAX_INSTANCES = 365;

	/**
	 * @param string $ical_string Raw iCal body.
	 * @return array<int,array<string,mixed>>
	 */
	public function parse( $ical_string ) {
		$events  = array();
		$unfolded = preg_replace( "/\r\n[ \t]/", '', (string) $ical_string );
		if ( ! is_string( $unfolded ) ) {
			return array();
		}

		if ( ! preg_match_all( '/BEGIN:VEVENT(.+?)END:VEVENT/s', $unfolded, $matches ) ) {
			return array();
		}

		foreach ( $matches[1] as $vevent ) {
			$props = $this->parse_properties( $vevent );
			if ( empty( $props['DTSTART'] ) ) {
				continue;
			}
			if ( ! empty( $props['RRULE'] ) ) {
				$events = array_merge( $events, $this->expand_rrule( $props ) );
			} else {
				$events[] = $this->normalise( $props, false );
			}
		}

		return $events;
	}

	/**
	 * @param string $vevent VEVENT body.
	 * @return array<string,string>
	 */
	private function parse_properties( $vevent ) {
		$props = array();
		$lines = preg_split( '/\r\n|\n|\r/', $vevent );
		if ( ! is_array( $lines ) ) {
			return $props;
		}
		foreach ( $lines as $line ) {
			if ( false === strpos( $line, ':' ) ) {
				continue;
			}
			list( $key, $val ) = explode( ':', $line, 2 );
			$key               = explode( ';', trim( $key ) );
			$key               = strtoupper( $key[0] );
			$props[ $key ]     = trim( $val );
		}
		return $props;
	}

	/**
	 * @param array<string,string> $props VEVENT properties.
	 * @param bool                 $recurrence Expanded instance.
	 * @return array<string,mixed>
	 */
	private function normalise( $props, $recurrence ) {
		$start = $this->parse_dt( isset( $props['DTSTART'] ) ? $props['DTSTART'] : '' );
		$end   = $this->parse_dt( isset( $props['DTEND'] ) ? $props['DTEND'] : ( isset( $props['DTSTART'] ) ? $props['DTSTART'] : '' ) );
		$uid   = isset( $props['UID'] ) ? sanitize_text_field( $props['UID'] ) : md5( wp_json_encode( $props ) );

		return array(
			'id'          => $uid,
			'title'       => $this->decode( isset( $props['SUMMARY'] ) ? $props['SUMMARY'] : '' ),
			'start'       => $start->format( 'Y-m-d H:i' ),
			'end'         => $end->format( 'Y-m-d H:i' ),
			'all_day'     => isset( $props['DTSTART'] ) && 8 === strlen( preg_replace( '/[^0-9]/', '', $props['DTSTART'] ) ),
			'url'         => isset( $props['URL'] ) ? esc_url_raw( $props['URL'] ) : '',
			'description' => $this->decode( isset( $props['DESCRIPTION'] ) ? $props['DESCRIPTION'] : '' ),
			'location'    => $this->decode( isset( $props['LOCATION'] ) ? $props['LOCATION'] : '' ),
			'recurrence'  => $recurrence,
		);
	}

	/**
	 * @param string $dt iCal datetime.
	 * @return \DateTimeImmutable
	 */
	private function parse_dt( $dt ) {
		$dt  = preg_replace( '/[^0-9TZ]/', '', (string) $dt );
		$tz  = ( is_string( $dt ) && 'Z' === substr( $dt, -1 ) ) ? new \DateTimeZone( 'UTC' ) : wp_timezone();
		$fmt = ( is_string( $dt ) && 8 === strlen( $dt ) ) ? 'Ymd' : ( ( is_string( $dt ) && 'Z' === substr( $dt, -1 ) ) ? 'Ymd\THis\Z' : 'Ymd\THis' );
		$out = \DateTimeImmutable::createFromFormat( $fmt, (string) $dt, $tz );
		return $out ? $out : new \DateTimeImmutable( 'now', wp_timezone() );
	}

	/**
	 * @param array<string,string> $props VEVENT properties.
	 * @return array<int,array<string,mixed>>
	 */
	private function expand_rrule( $props ) {
		$rrule   = $this->parse_rrule_string( isset( $props['RRULE'] ) ? $props['RRULE'] : '' );
		$base    = $this->normalise( $props, true );
		$exdates = $this->parse_exdates( isset( $props['EXDATE'] ) ? $props['EXDATE'] : '' );
		$start   = $this->parse_dt( $props['DTSTART'] );
		$end     = $this->parse_dt( isset( $props['DTEND'] ) ? $props['DTEND'] : $props['DTSTART'] );
		$span    = $end->getTimestamp() - $start->getTimestamp();
		if ( $span < 0 ) {
			$span = 0;
		}

		$freq     = isset( $rrule['FREQ'] ) ? strtoupper( $rrule['FREQ'] ) : 'DAILY';
		$interval = isset( $rrule['INTERVAL'] ) ? max( 1, (int) $rrule['INTERVAL'] ) : 1;
		$count    = isset( $rrule['COUNT'] ) ? min( self::MAX_INSTANCES, (int) $rrule['COUNT'] ) : self::MAX_INSTANCES;
		$until    = ! empty( $rrule['UNTIL'] ) ? $this->parse_dt( $rrule['UNTIL'] ) : $start->modify( '+2 years' );
		$byday    = ! empty( $rrule['BYDAY'] ) ? array_map( 'strtoupper', explode( ',', $rrule['BYDAY'] ) ) : array();
		$bymday   = ! empty( $rrule['BYMONTHDAY'] ) ? array_map( 'intval', explode( ',', $rrule['BYMONTHDAY'] ) ) : array();

		$instances = array();
		$cursor    = $start;
		$added     = 0;
		$guard     = 0;

		while ( $added < $count && $cursor <= $until && $guard < 800 ) {
			$guard++;
			$key = $cursor->format( 'Y-m-d' );
			$ok  = ! in_array( $key, $exdates, true );
			if ( $ok && ! empty( $byday ) ) {
				$map = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA' );
				$ok  = in_array( $map[ (int) $cursor->format( 'w' ) ], $byday, true );
			}
			if ( $ok && ! empty( $bymday ) ) {
				$ok = in_array( (int) $cursor->format( 'j' ), $bymday, true );
			}
			if ( $ok ) {
				$inst          = $base;
				$inst['id']    = $base['id'] . '-' . $key;
				$inst['start'] = $cursor->format( 'Y-m-d H:i' );
				$inst['end']   = $cursor->modify( '+' . $span . ' seconds' )->format( 'Y-m-d H:i' );
				$instances[]   = $inst;
				$added++;
			}

			if ( 'WEEKLY' === $freq ) {
				$cursor = $cursor->modify( '+' . $interval . ' weeks' );
			} elseif ( 'MONTHLY' === $freq ) {
				$cursor = $cursor->modify( '+' . $interval . ' months' );
			} elseif ( 'YEARLY' === $freq ) {
				$cursor = $cursor->modify( '+' . $interval . ' years' );
			} else {
				$cursor = $cursor->modify( '+' . $interval . ' days' );
			}
		}

		return $instances;
	}

	/**
	 * @param string $rrule RRULE value.
	 * @return array<string,string>
	 */
	private function parse_rrule_string( $rrule ) {
		$out = array();
		foreach ( explode( ';', (string) $rrule ) as $part ) {
			if ( false === strpos( $part, '=' ) ) {
				continue;
			}
			list( $k, $v ) = explode( '=', $part, 2 );
			$out[ strtoupper( trim( $k ) ) ] = trim( $v );
		}
		return $out;
	}

	/**
	 * @param string $exdate EXDATE value.
	 * @return string[]
	 */
	private function parse_exdates( $exdate ) {
		$out = array();
		if ( '' === $exdate ) {
			return $out;
		}
		foreach ( explode( ',', $exdate ) as $part ) {
			$dt    = $this->parse_dt( $part );
			$out[] = $dt->format( 'Y-m-d' );
		}
		return $out;
	}

	/**
	 * @param string $s iCal escaped text.
	 * @return string
	 */
	private function decode( $s ) {
		$s = str_replace( array( '\\n', '\\N', '\\,', '\\;', '\\\\' ), array( "\n", "\n", ',', ';', '\\' ), (string) $s );
		return sanitize_text_field( $s );
	}
}
