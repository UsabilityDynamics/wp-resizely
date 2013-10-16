/**
 * Run Coverage Tests / Load Module
 *
 * @author potanin@UD
 */
module.exports = process.env.APP_COVERAGE ? require( './static/codex/lib-cov/ui' ) : require( './scripts/ui' );
