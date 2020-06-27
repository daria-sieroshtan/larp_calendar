const gulp = require("gulp");
var sass = require('gulp-sass');
sass.compiler = require('node-sass');
const autoprefixer = require("gulp-autoprefixer");
const csso = require("gulp-csso");
const concat = require('gulp-concat');
const del = require("del");

function clean() {
  return del(["../var/cache/prod/twig/**", "!../var/cache/prod/twig"], {force: true});
}

function buildCSS() {
  return gulp
    .src("./scss/**/*.scss")
    .pipe(concat('all.scss'))
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({
      cascade: false
    }))
    .pipe(csso())
    .pipe(gulp.dest("./css"));
}

function watchCSS() {
  gulp.watch("./scss/**/*.scss", gulp.series(clean, buildCSS));
}

const watch = gulp.parallel(watchCSS);

exports.clean = clean;
exports.buildCSS = buildCSS;
exports.watch = watch;
exports.default = gulp.series(clean,buildCSS);