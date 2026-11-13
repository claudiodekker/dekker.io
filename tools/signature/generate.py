import json, math, os, re, sys

here = os.path.dirname(os.path.abspath(__file__))
page = sys.argv[1] if len(sys.argv) > 1 else f"{here}/template.html"
traced = json.load(open(f"{here}/traced.json"))

# Hand-placed points for the rest of the name, read off the middle line of the lettering.
# A point repeated around a tip (up, tip, back down the same way) makes a clean reversal.
a_top = [(82, 70.6), (86, 67.3), (90, 64.9), (93, 64.2), (94.8, 65.4), (93.2, 68.3), (90, 71.6), (87, 74.6)]

# The d's bowl is a true slanted ellipse. The pen comes in along its top, doubles back around the
# left and bottom, and rides up the right side straight into the stem, so bowl and stem are one stroke.
def bowl(degrees):
    t = math.radians(degrees)
    return (129.2 + 8.4 * math.cos(t) * 0.766 + 5.4 * math.sin(t) * 0.643, 72.6 - 8.4 * math.cos(t) * 0.643 + 5.4 * math.sin(t) * 0.766)

d_top = [bowl(t) for t in (238, 260, 282, 304)] + [(134, 66.2)]
d_meets = (137.7, 67.5)
d_round = [bowl(t) for t in (214, 190, 164, 138, 112, 88, 66, 46)]
stem = [(140.3, 62.6), (146, 53.2), (151.6, 44.2)]
d_down = [bowl(t) for t in (46, 68)]

# The o is entered up its left side to the top, drawn round once, and left through a small loop at the top.
# The o is drawn at full height and then brought down to the height of the other small letters.
def small(point):
    return (157.3 + (point[0] - 157.3) * 0.84, 79.7 + (point[1] - 79.7) * 0.76)

o_left = [small(point) for point in [(157.6, 73.8), (158.8, 68.8), (161.2, 63.8)]]
o_top = small((164.6, 60.4))
o_closes = small((167.2, 61))

rest = (
    [(62.1, 78.6), (63.4, 79.5), (65.2, 79.6), (67.8, 78.8), (70.8, 77.4), (74, 75.6), (78, 73.6)] + a_top + [(85.2, 76.9), (82.5, 78.6), (79.5, 79.7), (77.2, 79.1), (76.4, 77.2), (77.6, 74.6)]
    + a_top + [(85.3, 77.3), (85, 79), (86.8, 80.1), (89.5, 79.5), (93, 77.6), (97, 75), (101, 72.4)]
    + [(104, 69.6), (107.2, 66.6), (104, 69.6), (101.2, 72.6), (99.5, 75.6), (99.6, 77.8), (101.5, 78.4), (105, 76.5), (108.5, 74), (111.2, 71.8)]
    + [(113.5, 69.6), (115.4, 67.9), (113.5, 69.6), (111.2, 72), (109.6, 75.3), (109.4, 78), (111, 79.2), (114.5, 77.8), (118, 75.4)]
    + d_top + [d_meets] + d_top[::-1] + d_round + [d_meets] + stem + [(156.2, 36.4)] + stem[::-1] + [d_meets] + d_down + [(133.9, 77.7), (134.9, 79.7), (137.4, 79)]
    + [(140.6, 76.2), (144, 72.8), (147.4, 69.6), (150, 67.3), (152.3, 65.2), (150, 67.3), (147.6, 70), (145.6, 73.6), (144.4, 77), (145, 79.4), (147.4, 80.2), (150.4, 79.2), (153.2, 77.6), (155.6, 75.9)]
    + o_left + [o_top, o_closes, o_top] + o_left[::-1] + [small(point) for point in [(157.3, 77.3), (158.8, 79.4), (161.2, 79.7), (164.4, 77.8), (167, 74.4), (168.9, 70), (169.8, 65.6), (169, 62.6)]] + [o_closes]
    + [small(point) for point in [(165.4, 60.5), (164.6, 62), (166, 63.7), (169.4, 64), (173.6, 62.6)]] + [(177.5, 64.8), (186, 61.6)]
)

def fmt(value):
    return f"{value:.2f}".rstrip('0').rstrip('.')

def curve(points, start=0):
    d = f"M{fmt(points[start][0])} {fmt(points[start][1])}"
    for i in range(start, len(points) - 1):
        before, start, end, after = points[max(i - 1, 0)], points[i], points[i + 1], points[min(i + 2, len(points) - 1)]
        c1 = (start[0] + (end[0] - before[0]) / 6, start[1] + (end[1] - before[1]) / 6)
        c2 = (end[0] - (after[0] - start[0]) / 6, end[1] - (after[1] - start[1]) / 6)
        d += f"C{fmt(c1[0])} {fmt(c1[1])} {fmt(c2[0])} {fmt(c2[1])} {fmt(end[0])} {fmt(end[1])}"
    return d

# The traced C and l are dense; thin them so the curve through it is as smooth as the rest.
bottom = min(range(len(traced)), key=lambda i: math.dist(traced[i], (64.5, 79.6)))
kept = traced[:max(i for i in range(bottom - 30, bottom) if traced[i][1] <= 76.5) + 1]
thinned = kept[::4] + ([kept[-1]] if (len(kept) - 1) % 4 else [])
written = [tuple(point) for point in thinned] + rest


# Proportions: a large capital that dips below the line, against small letters that are narrower and tighter.
joint = (55.7, 71.8)
joins = min(range(len(written)), key=lambda i: math.dist(written[i], joint))

def capital(point):
    return (joint[0] + (point[0] - joint[0]) * 1.215, joint[1] + (point[1] - joint[1]) * 1.215)

def lower(point):
    return (joint[0] + (point[0] - joint[0]) * 0.77, joint[1] + (point[1] - joint[1]) * 0.9)

written = [capital(point) for point in written[:joins]] + [lower(point) for point in written[joins:]]
name = curve(written)
tittle = curve([lower((155.4, 59.6)), lower((156, 58.7))])

beats = [(capital((63.5, 43.5)), 1.3)] + [(lower(point), pace) for point, pace in [((59, 71.8), 1.45), ((70.8, 77.4), 1.15), ((101, 72.4), 1.5), ((118, 75.4), 1.2), ((140.3, 62.6), 1.7), ((137.4, 79), 1.45), ((155.6, 75.9), 1.3), ((165.8, 66.2), 1.9)]]
tempo = "\n".join(f"    {{ x: {fmt(x)}, y: {fmt(y)}, pace: {pace} }}," for (x, y), pace in beats)

svg = f'''<svg class="signature" viewBox="0 14 176 80" role="img" aria-label="Claudio">
                <path pathLength="1" d="{name}"/>
                <path pathLength="1" d="{tittle}"/>
            </svg>'''

s = open(page).read()
s, drawn = re.subn(r'<svg class="signature".*?</svg>', lambda m: svg, s, count=1, flags=re.S)
s, paced = re.subn(r'const tempo = \[\n.*?\]', lambda m: f"const tempo = [\n{tempo}\n]", s, count=1, flags=re.S)
assert (drawn, paced) == (1, 1), (drawn, paced)
open(page, 'w').write(s)
