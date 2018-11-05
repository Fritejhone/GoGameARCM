let gulp = require('gulp'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    plumb = require('gulp-plumber'),
    babel = require('gulp-babel');

gulp.task('styles', function () {
    gulp.src('web/assets/sass/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest('./web/assets/css/'));
});

gulp.task('js', function () {
    return gulp.src('web/assets/js/*.js')
        .pipe(babel({
            presets: ['es2015']
        }))
        // .pipe(plumb())
        // .pipe(uglify())
        .pipe(concat('app.js'))
        .pipe(gulp.dest('./web/assets/js/prod/'))
});

//Watch task
gulp.task('default', function () {
    gulp.watch('web/assets/sass/*.scss', ['styles']);
    gulp.watch('web/assets/js/*.js', ['js']);
});
