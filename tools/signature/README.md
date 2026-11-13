# Signature generator

Builds the handwritten "Claudio" signature that draws itself on the homepage.

`traced.json` holds the points traced along the middle of a capital C from the Hurricane font and an l from Mrs Saint Delafield. `generate.py` adds the rest of the name from points placed by hand over the Mrs Saint Delafield lettering, scales the capital and the small letters, smooths everything into one SVG path, and writes that path and the pen's tempo into `template.html`.

```sh
python3 tools/signature/generate.py
```

`template.html` is the live homepage as one static page. Open it in a browser to see the signature draw at the pace a hand would write it, and press R to replay it. Pass another page's path to write into that page instead.

The homepage copy of the path lives in `source/_components/signature.blade.php`, rounded to one decimal, and its timing lives in the `sign` keyframes in `source/_assets/css/app.css`.
