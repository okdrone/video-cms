var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass');

gulp.task('default', ['video', 'sass'], function () {
    return true;
});

gulp.task('video', function () {
    return gulp.src('./src/js/video.js')
        .pipe(concat('video.js'))
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./public/js'));
});

gulp.task('flexible', function () {
    return gulp.src('./src/js/flexible/*.js')
        .pipe(concat('flexible.js'))
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./public/js/flexible'));
});

gulp.task('sass', function () {
    return gulp.src('./src/sass/*.scss')
        .pipe(sass({
            //outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(gulp.dest('./public/assets/css'));
});