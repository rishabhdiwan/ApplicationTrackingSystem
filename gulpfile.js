//Gulp Setup
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const rename = require('gulp-rename');
const cssnano = require('gulp-cssnano');
const uglify = require('gulp-uglify'); //Used to minify Js files
const browserSync = require('browser-sync').create();  //Saves changes automatically without refreshing

const { src, series, parallel, dest, watch } = require('gulp');

function style() {
  return src('./assets/scss/**/*.scss')
  .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
  .pipe(cssnano()) // Minify the CSS
  .pipe(rename({ suffix: '.min' })) // Rename the file to style.min.css
  .pipe(gulp.dest('./assets/build/css'))
  .pipe(browserSync.stream());
}

function scripts() {
  return src('./assets/js/**/*.js')
  .pipe(rename({ suffix: '.min' })) // Rename the file to custom.min.css
  .pipe(uglify())
  .pipe(gulp.dest('./assets/build/js'))
  .pipe(browserSync.stream());
}

function watchTask() {
  browserSync.init({
    proxy: "https://environment-setup.ddev.site/"
  });
  watch(['./assets/scss/**/*.scss'], { interval: 1000 }, parallel(style));
  watch(['./assets/js/**/*.js'], { interval: 1000 }, parallel(scripts));
}

exports.style = style;
exports.scripts = scripts;
exports.watchTask = watchTask;
exports.default = series(parallel(style, scripts), watchTask);