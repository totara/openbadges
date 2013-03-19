/**
 * Push badges to backpack.
 */
function addtobackpack(event, args) {
    OpenBadges.issue([args.assertion], function(errors, successes) { });
}