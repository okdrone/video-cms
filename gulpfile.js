var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    sourcemaps = require('gulp-sourcemaps');
    //sass = require('gulp-sass');

gulp.task('default', function() {
    return gulp.src('node/js/*.js')
        .pipe(uglify())
        .pipe(concat('city.js'))
        .pipe(rename({suffix: '.min', extname: '.js'}))
        .pipe(gulp.dest('public/js'))
});

gulp.task('sass', function() {
    return gulp.src('node/sass/*.sass')
        .pipe(sourcemaps.init())
        //.pipe(sass().on('error', sass.logError()))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/simpleboot/css'));
});