<?php // (C) Copyright Bobbing Wide 2015

/**
 * Library:	tags-users
 *
 * Prefix: tagu_
 * 
 * Here we're going to try to use CMB2 to implement the fields for users
 * Other options were to extend/reuse the following plugins:
 *
 * - wp-members
 * - BuddyPress XProfile
 *
 *
 */
 
 
/**
 * Lazy registration of player information
 *
 * Done in a separate file to try CMB2.
 * Here each player is a user of the site. 
 * This may not be what we actually want 
 * since there are many ex-players who do not need to be users of the site
 * Can they be disabled?
 * 
 *
 * Fields needed, over and above the standard ones are:
 * 
 * - photo
 * - handicap
 * - member type ( custom taxonomy )#
 * - phone
 * - Newsletter subscriber
 * -
 */ 
function tagu_lazy_register_players() {
	
	gobang();
	Echo "Not doing it this way yet!";


}
