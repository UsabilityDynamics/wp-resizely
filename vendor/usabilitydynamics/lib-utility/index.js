/**
 * Run Coverage Tests / Load Module
 *
 * @author potanin@UD
 */
module.exports = process.env.APP_COVERAGE ? require( './static/codex/lib-cov/scripts/utility' ) : require( './scripts/utility' );
