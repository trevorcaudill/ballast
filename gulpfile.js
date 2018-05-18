'use strict';

var gulp = require('gulp'),

    // SASS / CSS Processing
    bourbon = require('bourbon').includePaths,
    neat = require('bourbon-neat').includePaths,
    sass = require('gulp-sass'),
    autoprefixer = require('autoprefixer'),
    postcss = require('gulp-postcss'),
    cssminify = require('gulp-cssnano'),
    sourcemaps = require('gulp-sourcemaps'),
    sasslint = require('gulp-sass-lint'),

    // Utilities
    notify = require('gulp-notify'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename');

// Error Handling

function handleError() {
    var args = Array.prototype.slice.call(arguments);

    notify.onError({
        title: 'Task Failed [<%= error.message %>',
        message: 'See console.',
        sound: 'Sosumi'
    }).apply(this,args);

    gutil.beep();

    this.emit('end');
}

// Post CSS
gulp.task('postcss', function(){

    return gulp.src('assets/sass/style.scss')

        .pipe(plumber({
            errorHandler: handleError
        }))

        .pipe( sourcemaps.init())

        .pipe( sass({
            includePaths: [].concat( bourbon, neat ),
            errLogToConsole: true,
            outputStyle: 'expanded' // Options: nested, expanded, compact, compressed
        }))

        .pipe(postcss([
            autoprefixer({
                browsers: ['last 2 versions']
            })
        ]))

        .pipe(sourcemaps.write())

        .pipe( gulp.dest('./'))

        .pipe(notify({
            message: 'Styles are built.'
        }));
});

// Minify
gulp.task('cssminify', function() {
    return gulp.src('style.css')

    .pipe(plumber({
        errorHandler: handleError
    }))

    .pipe( cssminify({
        safe: true
    }))

    .pipe(rename('style.min.css'))

    .pipe(gulp.dest('./'))

    .pipe(notify({
        message: 'Stylesheet minified.'
    }));
});

// SASS Lint

gulp.task('sass-lint', function() {
    return gulp.src([
        'assets/sass/style.scss',
        '!assets/sass/base/html5-reset/_normalize.scss'
    ])
    .pipe(sasslint())
    .pipe(sasslint.format())
    .pipe(sasslint.failOnError())

    .pipe(notify({
        message: 'Linter complete.'
    }));
});

// Run Post CSS & Minify
gulp.task('styles', gulp.series('postcss', 'cssminify', 'sass-lint'));

// Watch Post CSS & Minify
gulp.task('watch', function () {
	gulp.watch('assets/sass/**/*.scss', gulp.series('styles'));
});