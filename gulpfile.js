var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass');

gulp.task('default', function() {
    return gulp.src('node/js/*.js')
        .pipe(uglify())
        .pipe(concat('city.js'))
        .pipe(rename({suffix: '.min', extname: '.js'}))
        .pipe(gulp.dest('public/js'))
});

gulp.task('js', function () {
    return gulp.src('./src/js/*.js')
        .pipe(concat('main.js'))
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
        .pipe(gulp.dest('./js'));
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