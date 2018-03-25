'use strict';
var src = 'assets/backend/';
var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');
var js_minifier = require('gulp-uglify');
var css_minifier = require('gulp-clean-css');

// font-awesome vendor
gulp.task('vendor:fontawesome', function()
{
    gulp.src('node_modules/font-awesome/css/*.*').pipe(gulp.dest('assets/vendor/font-awesome/css'));
    gulp.src('node_modules/font-awesome/fonts/*.*').pipe(gulp.dest('assets/vendor/font-awesome/fonts'));
});

// Compile css
gulp.task('css:compile', ['vendor:fontawesome'], function()
{
  return gulp.src(src+'sass/*.scss')
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
    .pipe(gulp.dest(src+'css'));
});

// Minify css
gulp.task('css:build', ['css:compile'], function()
{
  return gulp.src([src+'css/*.css', '!'+src+'css/*.min.css'])
    .pipe(css_minifier())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(src+'css'));
});

// Watch css
gulp.task('css:watch', function()
{
  return gulp.watch(src+'sass/*.scss', ['css:build']);
});

// Minify js
gulp.task('js:build', function()
{
  return gulp.src([src+'js/*.js', '!'+src+'js/*.min.js'])
    .pipe(js_minifier()).on('error', function(err){console.error('Error', err.toString());})
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(src+'js'));
});

// Watch css
gulp.task('js:watch', function()
{
  return gulp.watch([src+'js/*.js', '!'+src+'js/*.min.js'], ['js:build']);
});

// Default task
gulp.task('default', ['css:watch', 'js:watch']);
