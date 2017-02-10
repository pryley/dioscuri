var browserSync  = require( 'browser-sync' ).create();
var gulp         = require( 'gulp' );
var autoprefixer = require( 'gulp-autoprefixer' );
var cssnano      = require( 'gulp-cssnano' );
var gulpif       = require( 'gulp-if' );
var insert       = require( 'gulp-insert' );
var notify       = require( 'gulp-notify' );
var rename       = require( 'gulp-rename' );
var sass         = require( 'gulp-sass' );
var watch        = require( 'gulp-watch' );
var args         = require( 'yargs' ).argv;

var paths = {
	src: ['demo/**/*.scss'],
};

/* CSS Task
 -------------------------------------------------- */
gulp.task( 'css', function ()
{
	return gulp
		.src( paths.src, { base: '.' })
		.pipe( sass({ outputStyle: 'expanded' }).on( 'error', sass.logError ))
		.pipe( autoprefixer() )
		.pipe( gulpif( args.production, cssnano({
			discardComments: { removeAll: true }
		})))
		.pipe( rename( function( path ) {
			path.dirname = path.dirname.replace( 'demo', 'public/assets/css' );
		}))
		.pipe( gulp.dest( '.' ))
		.pipe( browserSync.stream() )
		.pipe( notify({
			message: 'CSS Task complete!',
			onLast : true
		}));
});

/* Watch Task
 -------------------------------------------------- */
gulp.task( 'watch', function ()
{
	browserSync.init({
		proxy: 'crazicherries.dev'
	});

	gulp.watch( paths.src, ['css'] );
	gulp.watch( 'public/*.php' ).on( 'change', browserSync.reload );
});

/* Default Task
 -------------------------------------------------- */
gulp.task( 'default', function ()
{
	gulp.start( 'css' );
});
