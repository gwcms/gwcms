/*!
FullCalendar Scheduler v5.11.5
Docs & License: https://fullcalendar.io/scheduler
(c) 2022 Adam Shaw
*/
var FullCalendar = function(e) {
    "use strict";
    var t = function(e, n) {
        return (t = Object.setPrototypeOf || {
            __proto__: []
        }instanceof Array && function(e, t) {
            e.__proto__ = t
        }
        || function(e, t) {
            for (var n in t)
                Object.prototype.hasOwnProperty.call(t, n) && (e[n] = t[n])
        }
        )(e, n)
    };
    function n(e, n) {
        if ("function" != typeof n && null !== n)
            throw new TypeError("Class extends value " + String(n) + " is not a constructor or null");
        function r() {
            this.constructor = e
        }
        t(e, n),
        e.prototype = null === n ? Object.create(n) : (r.prototype = n.prototype,
        new r)
    }
    var r = function() {
        return (r = Object.assign || function(e) {
            for (var t, n = 1, r = arguments.length; n < r; n++)
                for (var o in t = arguments[n])
                    Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
            return e
        }
        ).apply(this, arguments)
    };
    function o(e, t, n) {
        if (n || 2 === arguments.length)
            for (var r, o = 0, i = t.length; o < i; o++)
                !r && o in t || (r || (r = Array.prototype.slice.call(t, 0, o)),
                r[o] = t[o]);
        return e.concat(r || t)
    }
    var i, a, s, l, u, c, d, p, f = {}, h = [], v = /acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i;
    function g(e, t) {
        for (var n in t)
            e[n] = t[n];
        return e
    }
    function m(e) {
        var t = e.parentNode;
        t && t.removeChild(e)
    }
    function y(e, t, n) {
        var r, o, a, s = {};
        for (a in t)
            "key" == a ? r = t[a] : "ref" == a ? o = t[a] : s[a] = t[a];
        if (arguments.length > 2 && (s.children = arguments.length > 3 ? i.call(arguments, 2) : n),
        "function" == typeof e && null != e.defaultProps)
            for (a in e.defaultProps)
                void 0 === s[a] && (s[a] = e.defaultProps[a]);
        return S(e, s, r, o, null)
    }
    function S(e, t, n, r, o) {
        var i = {
            type: e,
            props: t,
            key: n,
            ref: r,
            __k: null,
            __: null,
            __b: 0,
            __e: null,
            __d: void 0,
            __c: null,
            __h: null,
            constructor: void 0,
            __v: null == o ? ++s : o
        };
        return null == o && null != a.vnode && a.vnode(i),
        i
    }
    function E(e) {
        return e.children
    }
    function b(e, t, n) {
        "-" === t[0] ? e.setProperty(t, null == n ? "" : n) : e[t] = null == n ? "" : "number" != typeof n || v.test(t) ? n : n + "px"
    }
    function C(e, t, n, r, o) {
        var i;
        e: if ("style" === t)
            if ("string" == typeof n)
                e.style.cssText = n;
            else {
                if ("string" == typeof r && (e.style.cssText = r = ""),
                r)
                    for (t in r)
                        n && t in n || b(e.style, t, "");
                if (n)
                    for (t in n)
                        r && n[t] === r[t] || b(e.style, t, n[t])
            }
        else if ("o" === t[0] && "n" === t[1])
            i = t !== (t = t.replace(/Capture$/, "")),
            t = t.toLowerCase()in e ? t.toLowerCase().slice(2) : t.slice(2),
            e.l || (e.l = {}),
            e.l[t + i] = n,
            n ? r || e.addEventListener(t, i ? R : D, i) : e.removeEventListener(t, i ? R : D, i);
        else if ("dangerouslySetInnerHTML" !== t) {
            if (o)
                t = t.replace(/xlink(H|:h)/, "h").replace(/sName$/, "s");
            else if ("width" !== t && "height" !== t && "href" !== t && "list" !== t && "form" !== t && "tabIndex" !== t && "download" !== t && t in e)
                try {
                    e[t] = null == n ? "" : n;
                    break e
                } catch (e) {}
            "function" == typeof n || (null == n || !1 === n && -1 == t.indexOf("-") ? e.removeAttribute(t) : e.setAttribute(t, n))
        }
    }
    function D(e) {
        l = !0;
        try {
            return this.l[e.type + !1](a.event ? a.event(e) : e)
        } finally {
            l = !1
        }
    }
    function R(e) {
        l = !0;
        try {
            return this.l[e.type + !0](a.event ? a.event(e) : e)
        } finally {
            l = !1
        }
    }
    function w(e, t) {
        this.props = e,
        this.context = t
    }
    function T(e, t) {
        if (null == t)
            return e.__ ? T(e.__, e.__.__k.indexOf(e) + 1) : null;
        for (var n; t < e.__k.length; t++)
            if (null != (n = e.__k[t]) && null != n.__e)
                return n.__e;
        return "function" == typeof e.type ? T(e) : null
    }
    function _(e) {
        var t, n;
        if (null != (e = e.__) && null != e.__c) {
            for (e.__e = e.__c.base = null,
            t = 0; t < e.__k.length; t++)
                if (null != (n = e.__k[t]) && null != n.__e) {
                    e.__e = e.__c.base = n.__e;
                    break
                }
            return _(e)
        }
    }
    function x(e) {
        l ? setTimeout(e) : d(e)
    }
    function k(e) {
        (!e.__d && (e.__d = !0) && u.push(e) && !M.__r++ || c !== a.debounceRendering) && ((c = a.debounceRendering) || x)(M)
    }
    function M() {
        var e, t, n, r, o, i, a, s;
        for (u.sort((function(e, t) {
            return e.__v.__b - t.__v.__b
        }
        )); e = u.shift(); )
            e.__d && (t = u.length,
            r = void 0,
            o = void 0,
            a = (i = (n = e).__v).__e,
            (s = n.__P) && (r = [],
            (o = g({}, i)).__v = i.__v + 1,
            A(s, i, o, n.__n, void 0 !== s.ownerSVGElement, null != i.__h ? [a] : null, r, null == a ? T(i) : a, i.__h),
            W(r, i),
            i.__e != a && _(i)),
            u.length > t && u.sort((function(e, t) {
                return e.__v.__b - t.__v.__b
            }
            )));
        M.__r = 0
    }
    function I(e, t, n, r, o, i, a, s, l, u) {
        var c, d, p, v, g, m, y, b = r && r.__k || h, C = b.length;
        for (n.__k = [],
        c = 0; c < t.length; c++)
            if (null != (v = n.__k[c] = null == (v = t[c]) || "boolean" == typeof v ? null : "string" == typeof v || "number" == typeof v || "bigint" == typeof v ? S(null, v, null, null, v) : Array.isArray(v) ? S(E, {
                children: v
            }, null, null, null) : v.__b > 0 ? S(v.type, v.props, v.key, v.ref ? v.ref : null, v.__v) : v)) {
                if (v.__ = n,
                v.__b = n.__b + 1,
                null === (p = b[c]) || p && v.key == p.key && v.type === p.type)
                    b[c] = void 0;
                else
                    for (d = 0; d < C; d++) {
                        if ((p = b[d]) && v.key == p.key && v.type === p.type) {
                            b[d] = void 0;
                            break
                        }
                        p = null
                    }
                A(e, v, p = p || f, o, i, a, s, l, u),
                g = v.__e,
                (d = v.ref) && p.ref != d && (y || (y = []),
                p.ref && y.push(p.ref, null, v),
                y.push(d, v.__c || g, v)),
                null != g ? (null == m && (m = g),
                "function" == typeof v.type && v.__k === p.__k ? v.__d = l = P(v, l, e) : l = H(e, v, p, b, g, l),
                "function" == typeof n.type && (n.__d = l)) : l && p.__e == l && l.parentNode != e && (l = T(p))
            }
        for (n.__e = m,
        c = C; c--; )
            null != b[c] && ("function" == typeof n.type && null != b[c].__e && b[c].__e == n.__d && (n.__d = O(r).nextSibling),
            B(b[c], b[c]));
        if (y)
            for (c = 0; c < y.length; c++)
                U(y[c], y[++c], y[++c])
    }
    function P(e, t, n) {
        for (var r, o = e.__k, i = 0; o && i < o.length; i++)
            (r = o[i]) && (r.__ = e,
            t = "function" == typeof r.type ? P(r, t, n) : H(n, r, r, o, r.__e, t));
        return t
    }
    function N(e, t) {
        return t = t || [],
        null == e || "boolean" == typeof e || (Array.isArray(e) ? e.some((function(e) {
            N(e, t)
        }
        )) : t.push(e)),
        t
    }
    function H(e, t, n, r, o, i) {
        var a, s, l;
        if (void 0 !== t.__d)
            a = t.__d,
            t.__d = void 0;
        else if (null == n || o != i || null == o.parentNode)
            e: if (null == i || i.parentNode !== e)
                e.appendChild(o),
                a = null;
            else {
                for (s = i,
                l = 0; (s = s.nextSibling) && l < r.length; l += 1)
                    if (s == o)
                        break e;
                e.insertBefore(o, i),
                a = i
            }
        return void 0 !== a ? a : o.nextSibling
    }
    function O(e) {
        var t, n, r;
        if (null == e.type || "string" == typeof e.type)
            return e.__e;
        if (e.__k)
            for (t = e.__k.length - 1; t >= 0; t--)
                if ((n = e.__k[t]) && (r = O(n)))
                    return r;
        return null
    }
    function A(e, t, n, r, o, i, s, l, u) {
        var c, d, p, f, h, v, m, y, S, b, C, D, R, T, _, x = t.type;
        if (void 0 !== t.constructor)
            return null;
        null != n.__h && (u = n.__h,
        l = t.__e = n.__e,
        t.__h = null,
        i = [l]),
        (c = a.__b) && c(t);
        try {
            e: if ("function" == typeof x) {
                if (y = t.props,
                S = (c = x.contextType) && r[c.__c],
                b = c ? S ? S.props.value : c.__ : r,
                n.__c ? m = (d = t.__c = n.__c).__ = d.__E : ("prototype"in x && x.prototype.render ? t.__c = d = new x(y,b) : (t.__c = d = new w(y,b),
                d.constructor = x,
                d.render = z),
                S && S.sub(d),
                d.props = y,
                d.state || (d.state = {}),
                d.context = b,
                d.__n = r,
                p = d.__d = !0,
                d.__h = [],
                d._sb = []),
                null == d.__s && (d.__s = d.state),
                null != x.getDerivedStateFromProps && (d.__s == d.state && (d.__s = g({}, d.__s)),
                g(d.__s, x.getDerivedStateFromProps(y, d.__s))),
                f = d.props,
                h = d.state,
                d.__v = t,
                p)
                    null == x.getDerivedStateFromProps && null != d.componentWillMount && d.componentWillMount(),
                    null != d.componentDidMount && d.__h.push(d.componentDidMount);
                else {
                    if (null == x.getDerivedStateFromProps && y !== f && null != d.componentWillReceiveProps && d.componentWillReceiveProps(y, b),
                    !d.__e && null != d.shouldComponentUpdate && !1 === d.shouldComponentUpdate(y, d.__s, b) || t.__v === n.__v) {
                        for (t.__v !== n.__v && (d.props = y,
                        d.state = d.__s,
                        d.__d = !1),
                        t.__e = n.__e,
                        t.__k = n.__k,
                        t.__k.forEach((function(e) {
                            e && (e.__ = t)
                        }
                        )),
                        C = 0; C < d._sb.length; C++)
                            d.__h.push(d._sb[C]);
                        d._sb = [],
                        d.__h.length && s.push(d);
                        break e
                    }
                    null != d.componentWillUpdate && d.componentWillUpdate(y, d.__s, b),
                    null != d.componentDidUpdate && d.__h.push((function() {
                        d.componentDidUpdate(f, h, v)
                    }
                    ))
                }
                if (d.context = b,
                d.props = y,
                d.__P = e,
                D = a.__r,
                R = 0,
                "prototype"in x && x.prototype.render) {
                    for (d.state = d.__s,
                    d.__d = !1,
                    D && D(t),
                    c = d.render(d.props, d.state, d.context),
                    T = 0; T < d._sb.length; T++)
                        d.__h.push(d._sb[T]);
                    d._sb = []
                } else
                    do {
                        d.__d = !1,
                        D && D(t),
                        c = d.render(d.props, d.state, d.context),
                        d.state = d.__s
                    } while (d.__d && ++R < 25);
                d.state = d.__s,
                null != d.getChildContext && (r = g(g({}, r), d.getChildContext())),
                p || null == d.getSnapshotBeforeUpdate || (v = d.getSnapshotBeforeUpdate(f, h)),
                _ = null != c && c.type === E && null == c.key ? c.props.children : c,
                I(e, Array.isArray(_) ? _ : [_], t, n, r, o, i, s, l, u),
                d.base = t.__e,
                t.__h = null,
                d.__h.length && s.push(d),
                m && (d.__E = d.__ = null),
                d.__e = !1
            } else
                null == i && t.__v === n.__v ? (t.__k = n.__k,
                t.__e = n.__e) : t.__e = L(n.__e, t, n, r, o, i, s, u);
            (c = a.diffed) && c(t)
        } catch (e) {
            t.__v = null,
            (u || null != i) && (t.__e = l,
            t.__h = !!u,
            i[i.indexOf(l)] = null),
            a.__e(e, t, n)
        }
    }
    function W(e, t) {
        a.__c && a.__c(t, e),
        e.some((function(t) {
            try {
                e = t.__h,
                t.__h = [],
                e.some((function(e) {
                    e.call(t)
                }
                ))
            } catch (e) {
                a.__e(e, t.__v)
            }
        }
        ))
    }
    function L(e, t, n, r, o, a, s, l) {
        var u, c, d, p = n.props, h = t.props, v = t.type, g = 0;
        if ("svg" === v && (o = !0),
        null != a)
            for (; g < a.length; g++)
                if ((u = a[g]) && "setAttribute"in u == !!v && (v ? u.localName === v : 3 === u.nodeType)) {
                    e = u,
                    a[g] = null;
                    break
                }
        if (null == e) {
            if (null === v)
                return document.createTextNode(h);
            e = o ? document.createElementNS("http://www.w3.org/2000/svg", v) : document.createElement(v, h.is && h),
            a = null,
            l = !1
        }
        if (null === v)
            p === h || l && e.data === h || (e.data = h);
        else {
            if (a = a && i.call(e.childNodes),
            c = (p = n.props || f).dangerouslySetInnerHTML,
            d = h.dangerouslySetInnerHTML,
            !l) {
                if (null != a)
                    for (p = {},
                    g = 0; g < e.attributes.length; g++)
                        p[e.attributes[g].name] = e.attributes[g].value;
                (d || c) && (d && (c && d.__html == c.__html || d.__html === e.innerHTML) || (e.innerHTML = d && d.__html || ""))
            }
            if (function(e, t, n, r, o) {
                var i;
                for (i in n)
                    "children" === i || "key" === i || i in t || C(e, i, null, n[i], r);
                for (i in t)
                    o && "function" != typeof t[i] || "children" === i || "key" === i || "value" === i || "checked" === i || n[i] === t[i] || C(e, i, t[i], n[i], r)
            }(e, h, p, o, l),
            d)
                t.__k = [];
            else if (g = t.props.children,
            I(e, Array.isArray(g) ? g : [g], t, n, r, o && "foreignObject" !== v, a, s, a ? a[0] : n.__k && T(n, 0), l),
            null != a)
                for (g = a.length; g--; )
                    null != a[g] && m(a[g]);
            l || ("value"in h && void 0 !== (g = h.value) && (g !== e.value || "progress" === v && !g || "option" === v && g !== p.value) && C(e, "value", g, p.value, !1),
            "checked"in h && void 0 !== (g = h.checked) && g !== e.checked && C(e, "checked", g, p.checked, !1))
        }
        return e
    }
    function U(e, t, n) {
        try {
            "function" == typeof e ? e(t) : e.current = t
        } catch (e) {
            a.__e(e, n)
        }
    }
    function B(e, t, n) {
        var r, o;
        if (a.unmount && a.unmount(e),
        (r = e.ref) && (r.current && r.current !== e.__e || U(r, null, t)),
        null != (r = e.__c)) {
            if (r.componentWillUnmount)
                try {
                    r.componentWillUnmount()
                } catch (e) {
                    a.__e(e, t)
                }
            r.base = r.__P = null,
            e.__c = void 0
        }
        if (r = e.__k)
            for (o = 0; o < r.length; o++)
                r[o] && B(r[o], t, n || "function" != typeof e.type);
        n || null == e.__e || m(e.__e),
        e.__ = e.__e = e.__d = void 0
    }
    function z(e, t, n) {
        return this.constructor(e, n)
    }
    function V(e, t, n) {
        var r, o, s;
        a.__ && a.__(e, t),
        o = (r = "function" == typeof n) ? null : n && n.__k || t.__k,
        s = [],
        A(t, e = (!r && n || t).__k = y(E, null, [e]), o || f, f, void 0 !== t.ownerSVGElement, !r && n ? [n] : o ? null : t.firstChild ? i.call(t.childNodes) : null, s, !r && n ? n : o ? o.__e : t.firstChild, r),
        W(s, e)
    }
    i = h.slice,
    a = {
        __e: function(e, t, n, r) {
            for (var o, i, a; t = t.__; )
                if ((o = t.__c) && !o.__)
                    try {
                        if ((i = o.constructor) && null != i.getDerivedStateFromError && (o.setState(i.getDerivedStateFromError(e)),
                        a = o.__d),
                        null != o.componentDidCatch && (o.componentDidCatch(e, r || {}),
                        a = o.__d),
                        a)
                            return o.__E = o
                    } catch (t) {
                        e = t
                    }
            throw e
        }
    },
    s = 0,
    l = !1,
    w.prototype.setState = function(e, t) {
        var n;
        n = null != this.__s && this.__s !== this.state ? this.__s : this.__s = g({}, this.state),
        "function" == typeof e && (e = e(g({}, n), this.props)),
        e && g(n, e),
        null != e && this.__v && (t && this._sb.push(t),
        k(this))
    }
    ,
    w.prototype.forceUpdate = function(e) {
        this.__v && (this.__e = !0,
        e && this.__h.push(e),
        k(this))
    }
    ,
    w.prototype.render = E,
    u = [],
    d = "function" == typeof Promise ? Promise.prototype.then.bind(Promise.resolve()) : setTimeout,
    M.__r = 0,
    p = 0;
    var F, G, j, q = [], Y = [], Z = a.__b, X = a.__r, K = a.diffed, $ = a.__c, J = a.unmount;
    function Q() {
        for (var e; e = q.shift(); )
            if (e.__P && e.__H)
                try {
                    e.__H.__h.forEach(ne),
                    e.__H.__h.forEach(re),
                    e.__H.__h = []
                } catch (t) {
                    e.__H.__h = [],
                    a.__e(t, e.__v)
                }
    }
    a.__b = function(e) {
        F = null,
        Z && Z(e)
    }
    ,
    a.__r = function(e) {
        X && X(e);
        var t = (F = e.__c).__H;
        t && (G === F ? (t.__h = [],
        F.__h = [],
        t.__.forEach((function(e) {
            e.__N && (e.__ = e.__N),
            e.__V = Y,
            e.__N = e.i = void 0
        }
        ))) : (t.__h.forEach(ne),
        t.__h.forEach(re),
        t.__h = [])),
        G = F
    }
    ,
    a.diffed = function(e) {
        K && K(e);
        var t = e.__c;
        t && t.__H && (t.__H.__h.length && (1 !== q.push(t) && j === a.requestAnimationFrame || ((j = a.requestAnimationFrame) || te)(Q)),
        t.__H.__.forEach((function(e) {
            e.i && (e.__H = e.i),
            e.__V !== Y && (e.__ = e.__V),
            e.i = void 0,
            e.__V = Y
        }
        ))),
        G = F = null
    }
    ,
    a.__c = function(e, t) {
        t.some((function(e) {
            try {
                e.__h.forEach(ne),
                e.__h = e.__h.filter((function(e) {
                    return !e.__ || re(e)
                }
                ))
            } catch (n) {
                t.some((function(e) {
                    e.__h && (e.__h = [])
                }
                )),
                t = [],
                a.__e(n, e.__v)
            }
        }
        )),
        $ && $(e, t)
    }
    ,
    a.unmount = function(e) {
        J && J(e);
        var t, n = e.__c;
        n && n.__H && (n.__H.__.forEach((function(e) {
            try {
                ne(e)
            } catch (e) {
                t = e
            }
        }
        )),
        n.__H = void 0,
        t && a.__e(t, n.__v))
    }
    ;
    var ee = "function" == typeof requestAnimationFrame;
    function te(e) {
        var t, n = function() {
            clearTimeout(r),
            ee && cancelAnimationFrame(t),
            setTimeout(e)
        }, r = setTimeout(n, 100);
        ee && (t = requestAnimationFrame(n))
    }
    function ne(e) {
        var t = F
          , n = e.__c;
        "function" == typeof n && (e.__c = void 0,
        n()),
        F = t
    }
    function re(e) {
        var t = F;
        e.__c = e.__(),
        F = t
    }
    function oe(e, t) {
        for (var n in e)
            if ("__source" !== n && !(n in t))
                return !0;
        for (var r in t)
            if ("__source" !== r && e[r] !== t[r])
                return !0;
        return !1
    }
    function ie(e) {
        this.props = e
    }
    (ie.prototype = new w).isPureReactComponent = !0,
    ie.prototype.shouldComponentUpdate = function(e, t) {
        return oe(this.props, e) || oe(this.state, t)
    }
    ;
    var ae = a.__b;
    a.__b = function(e) {
        e.type && e.type.__f && e.ref && (e.props.ref = e.ref,
        e.ref = null),
        ae && ae(e)
    }
    ;
    var se = a.__e;
    a.__e = function(e, t, n, r) {
        if (e.then)
            for (var o, i = t; i = i.__; )
                if ((o = i.__c) && o.__c)
                    return null == t.__e && (t.__e = n.__e,
                    t.__k = n.__k),
                    o.__c(e, t);
        se(e, t, n, r)
    }
    ;
    var le = a.unmount;
    function ue(e, t, n) {
        return e && (e.__c && e.__c.__H && (e.__c.__H.__.forEach((function(e) {
            "function" == typeof e.__c && e.__c()
        }
        )),
        e.__c.__H = null),
        null != (e = function(e, t) {
            for (var n in t)
                e[n] = t[n];
            return e
        }({}, e)).__c && (e.__c.__P === n && (e.__c.__P = t),
        e.__c = null),
        e.__k = e.__k && e.__k.map((function(e) {
            return ue(e, t, n)
        }
        ))),
        e
    }
    function ce(e, t, n) {
        return e && (e.__v = null,
        e.__k = e.__k && e.__k.map((function(e) {
            return ce(e, t, n)
        }
        )),
        e.__c && e.__c.__P === t && (e.__e && n.insertBefore(e.__e, e.__d),
        e.__c.__e = !0,
        e.__c.__P = n)),
        e
    }
    function de() {
        this.__u = 0,
        this.t = null,
        this.__b = null
    }
    function pe(e) {
        var t = e.__.__c;
        return t && t.__a && t.__a(e)
    }
    function fe() {
        this.u = null,
        this.o = null
    }
    a.unmount = function(e) {
        var t = e.__c;
        t && t.__R && t.__R(),
        t && !0 === e.__h && (e.type = null),
        le && le(e)
    }
    ,
    (de.prototype = new w).__c = function(e, t) {
        var n = t.__c
          , r = this;
        null == r.t && (r.t = []),
        r.t.push(n);
        var o = pe(r.__v)
          , i = !1
          , a = function() {
            i || (i = !0,
            n.__R = null,
            o ? o(s) : s())
        };
        n.__R = a;
        var s = function() {
            if (!--r.__u) {
                if (r.state.__a) {
                    var e = r.state.__a;
                    r.__v.__k[0] = ce(e, e.__c.__P, e.__c.__O)
                }
                var t;
                for (r.setState({
                    __a: r.__b = null
                }); t = r.t.pop(); )
                    t.forceUpdate()
            }
        }
          , l = !0 === t.__h;
        r.__u++ || l || r.setState({
            __a: r.__b = r.__v.__k[0]
        }),
        e.then(a, a)
    }
    ,
    de.prototype.componentWillUnmount = function() {
        this.t = []
    }
    ,
    de.prototype.render = function(e, t) {
        if (this.__b) {
            if (this.__v.__k) {
                var n = document.createElement("div")
                  , r = this.__v.__k[0].__c;
                this.__v.__k[0] = ue(this.__b, n, r.__O = r.__P)
            }
            this.__b = null
        }
        var o = t.__a && y(E, null, e.fallback);
        return o && (o.__h = null),
        [y(E, null, t.__a ? null : e.children), o]
    }
    ;
    var he = function(e, t, n) {
        if (++n[1] === n[0] && e.o.delete(t),
        e.props.revealOrder && ("t" !== e.props.revealOrder[0] || !e.o.size))
            for (n = e.u; n; ) {
                for (; n.length > 3; )
                    n.pop()();
                if (n[1] < n[0])
                    break;
                e.u = n = n[2]
            }
    };
    function ve(e) {
        return this.getChildContext = function() {
            return e.context
        }
        ,
        e.children
    }
    function ge(e) {
        var t = this
          , n = e.i;
        t.componentWillUnmount = function() {
            V(null, t.l),
            t.l = null,
            t.i = null
        }
        ,
        t.i && t.i !== n && t.componentWillUnmount(),
        e.__v ? (t.l || (t.i = n,
        t.l = {
            nodeType: 1,
            parentNode: n,
            childNodes: [],
            appendChild: function(e) {
                this.childNodes.push(e),
                t.i.appendChild(e)
            },
            insertBefore: function(e, n) {
                this.childNodes.push(e),
                t.i.appendChild(e)
            },
            removeChild: function(e) {
                this.childNodes.splice(this.childNodes.indexOf(e) >>> 1, 1),
                t.i.removeChild(e)
            }
        }),
        V(y(ve, {
            context: t.context
        }, e.__v), t.l)) : t.l && t.componentWillUnmount()
    }
    (fe.prototype = new w).__a = function(e) {
        var t = this
          , n = pe(t.__v)
          , r = t.o.get(e);
        return r[0]++,
        function(o) {
            var i = function() {
                t.props.revealOrder ? (r.push(o),
                he(t, e, r)) : o()
            };
            n ? n(i) : i()
        }
    }
    ,
    fe.prototype.render = function(e) {
        this.u = null,
        this.o = new Map;
        var t = N(e.children);
        e.revealOrder && "b" === e.revealOrder[0] && t.reverse();
        for (var n = t.length; n--; )
            this.o.set(t[n], this.u = [1, 0, this.u]);
        return e.children
    }
    ,
    fe.prototype.componentDidUpdate = fe.prototype.componentDidMount = function() {
        var e = this;
        this.o.forEach((function(t, n) {
            he(e, n, t)
        }
        ))
    }
    ;
    var me = "undefined" != typeof Symbol && Symbol.for && Symbol.for("react.element") || 60103
      , ye = /^(?:accent|alignment|arabic|baseline|cap|clip(?!PathU)|color|dominant|fill|flood|font|glyph(?!R)|horiz|image|letter|lighting|marker(?!H|W|U)|overline|paint|pointer|shape|stop|strikethrough|stroke|text(?!L)|transform|underline|unicode|units|v|vector|vert|word|writing|x(?!C))[A-Z]/
      , Se = "undefined" != typeof document
      , Ee = function(e) {
        return ("undefined" != typeof Symbol && "symbol" == typeof Symbol() ? /fil|che|rad/i : /fil|che|ra/i).test(e)
    };
    w.prototype.isReactComponent = {},
    ["componentWillMount", "componentWillReceiveProps", "componentWillUpdate"].forEach((function(e) {
        Object.defineProperty(w.prototype, e, {
            configurable: !0,
            get: function() {
                return this["UNSAFE_" + e]
            },
            set: function(t) {
                Object.defineProperty(this, e, {
                    configurable: !0,
                    writable: !0,
                    value: t
                })
            }
        })
    }
    ));
    var be = a.event;
    function Ce() {}
    function De() {
        return this.cancelBubble
    }
    function Re() {
        return this.defaultPrevented
    }
    a.event = function(e) {
        return be && (e = be(e)),
        e.persist = Ce,
        e.isPropagationStopped = De,
        e.isDefaultPrevented = Re,
        e.nativeEvent = e
    }
    ;
    var we = {
        configurable: !0,
        get: function() {
            return this.class
        }
    }
      , Te = a.vnode;
    a.vnode = function(e) {
        var t = e.type
          , n = e.props
          , r = n;
        if ("string" == typeof t) {
            var o = -1 === t.indexOf("-");
            for (var i in r = {},
            n) {
                var a = n[i];
                Se && "children" === i && "noscript" === t || "value" === i && "defaultValue"in n && null == a || ("defaultValue" === i && "value"in n && null == n.value ? i = "value" : "download" === i && !0 === a ? a = "" : /ondoubleclick/i.test(i) ? i = "ondblclick" : /^onchange(textarea|input)/i.test(i + t) && !Ee(n.type) ? i = "oninput" : /^onfocus$/i.test(i) ? i = "onfocusin" : /^onblur$/i.test(i) ? i = "onfocusout" : /^on(Ani|Tra|Tou|BeforeInp|Compo)/.test(i) ? i = i.toLowerCase() : o && ye.test(i) ? i = i.replace(/[A-Z0-9]/g, "-$&").toLowerCase() : null === a && (a = void 0),
                /^oninput$/i.test(i) && (i = i.toLowerCase(),
                r[i] && (i = "oninputCapture")),
                r[i] = a)
            }
            "select" == t && r.multiple && Array.isArray(r.value) && (r.value = N(n.children).forEach((function(e) {
                e.props.selected = -1 != r.value.indexOf(e.props.value)
            }
            ))),
            "select" == t && null != r.defaultValue && (r.value = N(n.children).forEach((function(e) {
                e.props.selected = r.multiple ? -1 != r.defaultValue.indexOf(e.props.value) : r.defaultValue == e.props.value
            }
            ))),
            e.props = r,
            n.class != n.className && (we.enumerable = "className"in n,
            null != n.className && (r.class = n.className),
            Object.defineProperty(r, "className", we))
        }
        e.$$typeof = me,
        Te && Te(e)
    }
    ;
    var _e = a.__r;
    a.__r = function(e) {
        _e && _e(e)
    }
    ;
    var xe = "undefined" != typeof globalThis ? globalThis : window;
    xe.FullCalendarVDom ? console.warn("FullCalendar VDOM already loaded") : xe.FullCalendarVDom = {
        Component: w,
        createElement: y,
        render: V,
        createRef: function() {
            return {
                current: null
            }
        },
        Fragment: E,
        createContext: function(e) {
            var t = function(e, t) {
                var n = {
                    __c: t = "__cC" + p++,
                    __: e,
                    Consumer: function(e, t) {
                        return e.children(t)
                    },
                    Provider: function(e) {
                        var n, r;
                        return this.getChildContext || (n = [],
                        (r = {})[t] = this,
                        this.getChildContext = function() {
                            return r
                        }
                        ,
                        this.shouldComponentUpdate = function(e) {
                            this.props.value !== e.value && n.some((function(e) {
                                e.__e = !0,
                                k(e)
                            }
                            ))
                        }
                        ,
                        this.sub = function(e) {
                            n.push(e);
                            var t = e.componentWillUnmount;
                            e.componentWillUnmount = function() {
                                n.splice(n.indexOf(e), 1),
                                t && t.call(e)
                            }
                        }
                        ),
                        e.children
                    }
                };
                return n.Provider.__ = n.Consumer.contextType = n
            }(e)
              , n = t.Provider;
            return t.Provider = function() {
                var e = this
                  , t = !this.getChildContext
                  , r = n.apply(this, arguments);
                if (t) {
                    var o = [];
                    this.shouldComponentUpdate = function(t) {
                        e.props.value !== t.value && o.forEach((function(e) {
                            e.context = t.value,
                            e.forceUpdate()
                        }
                        ))
                    }
                    ,
                    this.sub = function(e) {
                        o.push(e);
                        var t = e.componentWillUnmount;
                        e.componentWillUnmount = function() {
                            o.splice(o.indexOf(e), 1),
                            t && t.call(e)
                        }
                    }
                }
                return r
            }
            ,
            t
        },
        createPortal: function(e, t) {
            var n = y(ge, {
                __v: e,
                i: t
            });
            return n.containerInfo = t,
            n
        },
        flushSync: function(e) {
            e();
            var t = a.debounceRendering
              , n = [];
            function r(e) {
                n.push(e)
            }
            a.debounceRendering = r,
            V(y(ke, {}), document.createElement("div"));
            for (; n.length; )
                n.shift()();
            a.debounceRendering = t
        },
        unmountComponentAtNode: function(e) {
            V(null, e)
        }
    };
    var ke = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            return y("div", {})
        }
        ,
        t.prototype.componentDidMount = function() {
            this.setState({})
        }
        ,
        t
    }(w);
    var Me = function() {
        function e(e, t) {
            this.context = e,
            this.internalEventSource = t
        }
        return e.prototype.remove = function() {
            this.context.dispatch({
                type: "REMOVE_EVENT_SOURCE",
                sourceId: this.internalEventSource.sourceId
            })
        }
        ,
        e.prototype.refetch = function() {
            this.context.dispatch({
                type: "FETCH_EVENT_SOURCES",
                sourceIds: [this.internalEventSource.sourceId],
                isRefetch: !0
            })
        }
        ,
        Object.defineProperty(e.prototype, "id", {
            get: function() {
                return this.internalEventSource.publicId
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "url", {
            get: function() {
                return this.internalEventSource.meta.url
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "format", {
            get: function() {
                return this.internalEventSource.meta.format
            },
            enumerable: !1,
            configurable: !0
        }),
        e
    }();
    function Ie(e) {
        e.parentNode && e.parentNode.removeChild(e)
    }
    function Pe(e, t) {
        if (e.closest)
            return e.closest(t);
        if (!document.documentElement.contains(e))
            return null;
        do {
            if (Ne(e, t))
                return e;
            e = e.parentElement || e.parentNode
        } while (null !== e && 1 === e.nodeType);
        return null
    }
    function Ne(e, t) {
        return (e.matches || e.matchesSelector || e.msMatchesSelector).call(e, t)
    }
    function He(e, t) {
        for (var n = e instanceof HTMLElement ? [e] : e, r = [], o = 0; o < n.length; o += 1)
            for (var i = n[o].querySelectorAll(t), a = 0; a < i.length; a += 1)
                r.push(i[a]);
        return r
    }
    function Oe(e, t) {
        for (var n = e instanceof HTMLElement ? [e] : e, r = [], o = 0; o < n.length; o += 1)
            for (var i = n[o].children, a = 0; a < i.length; a += 1) {
                var s = i[a];
                t && !Ne(s, t) || r.push(s)
            }
        return r
    }
    var Ae = /(top|left|right|bottom|width|height)$/i;
    function We(e, t) {
        for (var n in t)
            Le(e, n, t[n])
    }
    function Le(e, t, n) {
        null == n ? e.style[t] = "" : "number" == typeof n && Ae.test(t) ? e.style[t] = n + "px" : e.style[t] = n
    }
    function Ue(e) {
        var t, n;
        return null !== (n = null === (t = e.composedPath) || void 0 === t ? void 0 : t.call(e)[0]) && void 0 !== n ? n : e.target
    }
    function Be(e) {
        return e.getRootNode ? e.getRootNode() : document
    }
    var ze = 0;
    function Ve() {
        return "fc-dom-" + (ze += 1)
    }
    function Fe(e) {
        e.preventDefault()
    }
    function Ge(e, t, n, r) {
        var o = function(e, t) {
            return function(n) {
                var r = Pe(n.target, e);
                r && t.call(r, n, r)
            }
        }(n, r);
        return e.addEventListener(t, o),
        function() {
            e.removeEventListener(t, o)
        }
    }
    var je = ["webkitTransitionEnd", "otransitionend", "oTransitionEnd", "msTransitionEnd", "transitionend"];
    function qe(e, t) {
        var n = function(r) {
            t(r),
            je.forEach((function(t) {
                e.removeEventListener(t, n)
            }
            ))
        };
        je.forEach((function(t) {
            e.addEventListener(t, n)
        }
        ))
    }
    function Ye(e) {
        return r({
            onClick: e
        }, Ze(e))
    }
    function Ze(e) {
        return {
            tabIndex: 0,
            onKeyDown: function(t) {
                "Enter" !== t.key && " " !== t.key || (e(t),
                t.preventDefault())
            }
        }
    }
    var Xe = 0;
    function Ke() {
        return String(Xe += 1)
    }
    function $e() {
        document.body.classList.add("fc-not-allowed")
    }
    function Je() {
        document.body.classList.remove("fc-not-allowed")
    }
    function Qe(e) {
        e.classList.add("fc-unselectable"),
        e.addEventListener("selectstart", Fe)
    }
    function et(e) {
        e.classList.remove("fc-unselectable"),
        e.removeEventListener("selectstart", Fe)
    }
    function tt(e) {
        e.addEventListener("contextmenu", Fe)
    }
    function nt(e) {
        e.removeEventListener("contextmenu", Fe)
    }
    function rt(e) {
        var t, n, r = [], o = [];
        for ("string" == typeof e ? o = e.split(/\s*,\s*/) : "function" == typeof e ? o = [e] : Array.isArray(e) && (o = e),
        t = 0; t < o.length; t += 1)
            "string" == typeof (n = o[t]) ? r.push("-" === n.charAt(0) ? {
                field: n.substring(1),
                order: -1
            } : {
                field: n,
                order: 1
            }) : "function" == typeof n && r.push({
                func: n
            });
        return r
    }
    function ot(e, t, n) {
        var r, o;
        for (r = 0; r < n.length; r += 1)
            if (o = it(e, t, n[r]))
                return o;
        return 0
    }
    function it(e, t, n) {
        return n.func ? n.func(e, t) : at(e[n.field], t[n.field]) * (n.order || 1)
    }
    function at(e, t) {
        return e || t ? null == t ? -1 : null == e ? 1 : "string" == typeof e || "string" == typeof t ? String(e).localeCompare(String(t)) : e - t : 0
    }
    function st(e, t) {
        var n = String(e);
        return "000".substr(0, t - n.length) + n
    }
    function lt(e, t, n) {
        return "function" == typeof e ? e.apply(void 0, t) : "string" == typeof e ? t.reduce((function(e, t, n) {
            return e.replace("$" + n, t || "")
        }
        ), e) : n
    }
    function ut(e, t) {
        return e - t
    }
    function ct(e) {
        return e % 1 == 0
    }
    function dt(e) {
        var t = e.querySelector(".fc-scrollgrid-shrink-frame")
          , n = e.querySelector(".fc-scrollgrid-shrink-cushion");
        if (!t)
            throw new Error("needs fc-scrollgrid-shrink-frame className");
        if (!n)
            throw new Error("needs fc-scrollgrid-shrink-cushion className");
        return e.getBoundingClientRect().width - t.getBoundingClientRect().width + n.getBoundingClientRect().width
    }
    var pt = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
    function ft(e, t) {
        var n = Tt(e);
        return n[2] += 7 * t,
        _t(n)
    }
    function ht(e, t) {
        var n = Tt(e);
        return n[2] += t,
        _t(n)
    }
    function vt(e, t) {
        var n = Tt(e);
        return n[6] += t,
        _t(n)
    }
    function gt(e, t) {
        return mt(e, t) / 7
    }
    function mt(e, t) {
        return (t.valueOf() - e.valueOf()) / 864e5
    }
    function yt(e, t) {
        var n = bt(e)
          , r = bt(t);
        return {
            years: 0,
            months: 0,
            days: Math.round(mt(n, r)),
            milliseconds: t.valueOf() - r.valueOf() - (e.valueOf() - n.valueOf())
        }
    }
    function St(e, t) {
        var n = Et(e, t);
        return null !== n && n % 7 == 0 ? n / 7 : null
    }
    function Et(e, t) {
        return kt(e) === kt(t) ? Math.round(mt(e, t)) : null
    }
    function bt(e) {
        return _t([e.getUTCFullYear(), e.getUTCMonth(), e.getUTCDate()])
    }
    function Ct(e, t, n, r) {
        var o = _t([t, 0, 1 + Dt(t, n, r)])
          , i = bt(e)
          , a = Math.round(mt(o, i));
        return Math.floor(a / 7) + 1
    }
    function Dt(e, t, n) {
        var r = 7 + t - n;
        return -((7 + _t([e, 0, r]).getUTCDay() - t) % 7) + r - 1
    }
    function Rt(e) {
        return [e.getFullYear(), e.getMonth(), e.getDate(), e.getHours(), e.getMinutes(), e.getSeconds(), e.getMilliseconds()]
    }
    function wt(e) {
        return new Date(e[0],e[1] || 0,null == e[2] ? 1 : e[2],e[3] || 0,e[4] || 0,e[5] || 0)
    }
    function Tt(e) {
        return [e.getUTCFullYear(), e.getUTCMonth(), e.getUTCDate(), e.getUTCHours(), e.getUTCMinutes(), e.getUTCSeconds(), e.getUTCMilliseconds()]
    }
    function _t(e) {
        return 1 === e.length && (e = e.concat([0])),
        new Date(Date.UTC.apply(Date, e))
    }
    function xt(e) {
        return !isNaN(e.valueOf())
    }
    function kt(e) {
        return 1e3 * e.getUTCHours() * 60 * 60 + 1e3 * e.getUTCMinutes() * 60 + 1e3 * e.getUTCSeconds() + e.getUTCMilliseconds()
    }
    function Mt(e, t, n, r) {
        return {
            instanceId: Ke(),
            defId: e,
            range: t,
            forcedStartTzo: null == n ? null : n,
            forcedEndTzo: null == r ? null : r
        }
    }
    var It = Object.prototype.hasOwnProperty;
    function Pt(e, t) {
        var n = {};
        if (t)
            for (var r in t) {
                for (var o = [], i = e.length - 1; i >= 0; i -= 1) {
                    var a = e[i][r];
                    if ("object" == typeof a && a)
                        o.unshift(a);
                    else if (void 0 !== a) {
                        n[r] = a;
                        break
                    }
                }
                o.length && (n[r] = Pt(o))
            }
        for (i = e.length - 1; i >= 0; i -= 1) {
            var s = e[i];
            for (var l in s)
                l in n || (n[l] = s[l])
        }
        return n
    }
    function Nt(e, t) {
        var n = {};
        for (var r in e)
            t(e[r], r) && (n[r] = e[r]);
        return n
    }
    function Ht(e, t) {
        var n = {};
        for (var r in e)
            n[r] = t(e[r], r);
        return n
    }
    function Ot(e) {
        for (var t = {}, n = 0, r = e; n < r.length; n++) {
            t[r[n]] = !0
        }
        return t
    }
    function At(e) {
        var t = [];
        for (var n in e)
            t.push(e[n]);
        return t
    }
    function Wt(e, t) {
        if (e === t)
            return !0;
        for (var n in e)
            if (It.call(e, n) && !(n in t))
                return !1;
        for (var n in t)
            if (It.call(t, n) && e[n] !== t[n])
                return !1;
        return !0
    }
    function Lt(e, t) {
        var n = [];
        for (var r in e)
            It.call(e, r) && (r in t || n.push(r));
        for (var r in t)
            It.call(t, r) && e[r] !== t[r] && n.push(r);
        return n
    }
    function Ut(e, t, n) {
        if (void 0 === n && (n = {}),
        e === t)
            return !0;
        for (var r in t)
            if (!(r in e) || !Bt(e[r], t[r], n[r]))
                return !1;
        for (var r in e)
            if (!(r in t))
                return !1;
        return !0
    }
    function Bt(e, t, n) {
        return e === t || !0 === n || !!n && n(e, t)
    }
    function zt(e, t, n, r) {
        void 0 === t && (t = 0),
        void 0 === r && (r = 1);
        var o = [];
        null == n && (n = Object.keys(e).length);
        for (var i = t; i < n; i += r) {
            var a = e[i];
            void 0 !== a && o.push(a)
        }
        return o
    }
    function Vt(e, t, n) {
        var r = n.dateEnv
          , o = n.pluginHooks
          , i = n.options
          , a = e.defs
          , s = e.instances;
        for (var l in s = Nt(s, (function(e) {
            return !a[e.defId].recurringDef
        }
        )),
        a) {
            var u = a[l];
            if (u.recurringDef) {
                var c = u.recurringDef.duration;
                c || (c = u.allDay ? i.defaultAllDayEventDuration : i.defaultTimedEventDuration);
                for (var d = 0, p = Ft(u, c, t, r, o.recurringTypes); d < p.length; d++) {
                    var f = p[d]
                      , h = Mt(l, {
                        start: f,
                        end: r.add(f, c)
                    });
                    s[h.instanceId] = h
                }
            }
        }
        return {
            defs: a,
            instances: s
        }
    }
    function Ft(e, t, n, r, o) {
        var i = o[e.recurringDef.typeId].expand(e.recurringDef.typeData, {
            start: r.subtract(n.start, t),
            end: n.end
        }, r);
        return e.allDay && (i = i.map(bt)),
        i
    }
    var Gt = ["years", "months", "days", "milliseconds"]
      , jt = /^(-?)(?:(\d+)\.)?(\d+):(\d\d)(?::(\d\d)(?:\.(\d\d\d))?)?/;
    function qt(e, t) {
        var n;
        return "string" == typeof e ? function(e) {
            var t = jt.exec(e);
            if (t) {
                var n = t[1] ? -1 : 1;
                return {
                    years: 0,
                    months: 0,
                    days: n * (t[2] ? parseInt(t[2], 10) : 0),
                    milliseconds: n * (60 * (t[3] ? parseInt(t[3], 10) : 0) * 60 * 1e3 + 60 * (t[4] ? parseInt(t[4], 10) : 0) * 1e3 + 1e3 * (t[5] ? parseInt(t[5], 10) : 0) + (t[6] ? parseInt(t[6], 10) : 0))
                }
            }
            return null
        }(e) : "object" == typeof e && e ? Yt(e) : "number" == typeof e ? Yt(((n = {})[t || "milliseconds"] = e,
        n)) : null
    }
    function Yt(e) {
        var t = {
            years: e.years || e.year || 0,
            months: e.months || e.month || 0,
            days: e.days || e.day || 0,
            milliseconds: 60 * (e.hours || e.hour || 0) * 60 * 1e3 + 60 * (e.minutes || e.minute || 0) * 1e3 + 1e3 * (e.seconds || e.second || 0) + (e.milliseconds || e.millisecond || e.ms || 0)
        }
          , n = e.weeks || e.week;
        return n && (t.days += 7 * n,
        t.specifiedWeeks = !0),
        t
    }
    function Zt(e) {
        return e.years || e.months || e.milliseconds ? 0 : e.days
    }
    function Xt(e, t) {
        return {
            years: e.years + t.years,
            months: e.months + t.months,
            days: e.days + t.days,
            milliseconds: e.milliseconds + t.milliseconds
        }
    }
    function Kt(e, t) {
        return {
            years: e.years * t,
            months: e.months * t,
            days: e.days * t,
            milliseconds: e.milliseconds * t
        }
    }
    function $t(e) {
        return en(e) / 864e5
    }
    function Jt(e) {
        return en(e) / 6e4
    }
    function Qt(e) {
        return en(e) / 1e3
    }
    function en(e) {
        return 31536e6 * e.years + 2592e6 * e.months + 864e5 * e.days + e.milliseconds
    }
    function tn(e, t) {
        for (var n = null, r = 0; r < Gt.length; r += 1) {
            var o = Gt[r];
            if (t[o]) {
                var i = e[o] / t[o];
                if (!ct(i) || null !== n && n !== i)
                    return null;
                n = i
            } else if (e[o])
                return null
        }
        return n
    }
    function nn(e) {
        var t = e.milliseconds;
        if (t) {
            if (t % 1e3 != 0)
                return {
                    unit: "millisecond",
                    value: t
                };
            if (t % 6e4 != 0)
                return {
                    unit: "second",
                    value: t / 1e3
                };
            if (t % 36e5 != 0)
                return {
                    unit: "minute",
                    value: t / 6e4
                };
            if (t)
                return {
                    unit: "hour",
                    value: t / 36e5
                }
        }
        return e.days ? e.specifiedWeeks && e.days % 7 == 0 ? {
            unit: "week",
            value: e.days / 7
        } : {
            unit: "day",
            value: e.days
        } : e.months ? {
            unit: "month",
            value: e.months
        } : e.years ? {
            unit: "year",
            value: e.years
        } : {
            unit: "millisecond",
            value: 0
        }
    }
    function rn(e, t, n) {
        void 0 === n && (n = !1);
        var r = e.toISOString();
        return r = r.replace(".000", ""),
        n && (r = r.replace("T00:00:00Z", "")),
        r.length > 10 && (null == t ? r = r.replace("Z", "") : 0 !== t && (r = r.replace("Z", sn(t, !0)))),
        r
    }
    function on(e) {
        return e.toISOString().replace(/T.*$/, "")
    }
    function an(e) {
        return st(e.getUTCHours(), 2) + ":" + st(e.getUTCMinutes(), 2) + ":" + st(e.getUTCSeconds(), 2)
    }
    function sn(e, t) {
        void 0 === t && (t = !1);
        var n = e < 0 ? "-" : "+"
          , r = Math.abs(e)
          , o = Math.floor(r / 60)
          , i = Math.round(r % 60);
        return t ? n + st(o, 2) + ":" + st(i, 2) : "GMT" + n + o + (i ? ":" + st(i, 2) : "")
    }
    function ln(e, t) {
        for (var n = 0, r = 0; r < e.length; )
            e[r] === t ? (e.splice(r, 1),
            n += 1) : r += 1;
        return n
    }
    function un(e, t, n) {
        if (e === t)
            return !0;
        var r, o = e.length;
        if (o !== t.length)
            return !1;
        for (r = 0; r < o; r += 1)
            if (!(n ? n(e[r], t[r]) : e[r] === t[r]))
                return !1;
        return !0
    }
    function cn(e, t, n) {
        var r, o;
        return function() {
            for (var i = [], a = 0; a < arguments.length; a++)
                i[a] = arguments[a];
            if (r) {
                if (!un(r, i)) {
                    n && n(o);
                    var s = e.apply(this, i);
                    t && t(s, o) || (o = s)
                }
            } else
                o = e.apply(this, i);
            return r = i,
            o
        }
    }
    function dn(e, t, n) {
        var r, o, i = this;
        return function(a) {
            if (r) {
                if (!Wt(r, a)) {
                    n && n(o);
                    var s = e.call(i, a);
                    t && t(s, o) || (o = s)
                }
            } else
                o = e.call(i, a);
            return r = a,
            o
        }
    }
    function pn(e, t, n) {
        var r = this
          , o = []
          , i = [];
        return function(a) {
            for (var s = o.length, l = a.length, u = 0; u < s; u += 1)
                if (a[u]) {
                    if (!un(o[u], a[u])) {
                        n && n(i[u]);
                        var c = e.apply(r, a[u]);
                        t && t(c, i[u]) || (i[u] = c)
                    }
                } else
                    n && n(i[u]);
            for (; u < l; u += 1)
                i[u] = e.apply(r, a[u]);
            return o = a,
            i.splice(l),
            i
        }
    }
    function fn(e, t, n) {
        var r = this
          , o = {}
          , i = {};
        return function(a) {
            var s = {};
            for (var l in a)
                if (i[l])
                    if (un(o[l], a[l]))
                        s[l] = i[l];
                    else {
                        n && n(i[l]);
                        var u = e.apply(r, a[l]);
                        s[l] = t && t(u, i[l]) ? i[l] : u
                    }
                else
                    s[l] = e.apply(r, a[l]);
            return o = a,
            i = s,
            s
        }
    }
    var hn = {
        week: 3,
        separator: 0,
        omitZeroMinute: 0,
        meridiem: 0,
        omitCommas: 0
    }
      , vn = {
        timeZoneName: 7,
        era: 6,
        year: 5,
        month: 4,
        day: 2,
        weekday: 2,
        hour: 1,
        minute: 1,
        second: 1
    }
      , gn = /\s*([ap])\.?m\.?/i
      , mn = /,/g
      , yn = /\s+/g
      , Sn = /\u200e/g
      , En = /UTC|GMT/
      , bn = function() {
        function e(e) {
            var t = {}
              , n = {}
              , r = 0;
            for (var o in e)
                o in hn ? (n[o] = e[o],
                r = Math.max(hn[o], r)) : (t[o] = e[o],
                o in vn && (r = Math.max(vn[o], r)));
            this.standardDateProps = t,
            this.extendedSettings = n,
            this.severity = r,
            this.buildFormattingFunc = cn(Cn)
        }
        return e.prototype.format = function(e, t) {
            return this.buildFormattingFunc(this.standardDateProps, this.extendedSettings, t)(e)
        }
        ,
        e.prototype.formatRange = function(e, t, n, r) {
            var o = this.standardDateProps
              , i = this.extendedSettings
              , a = function(e, t, n) {
                if (n.getMarkerYear(e) !== n.getMarkerYear(t))
                    return 5;
                if (n.getMarkerMonth(e) !== n.getMarkerMonth(t))
                    return 4;
                if (n.getMarkerDay(e) !== n.getMarkerDay(t))
                    return 2;
                if (kt(e) !== kt(t))
                    return 1;
                return 0
            }(e.marker, t.marker, n.calendarSystem);
            if (!a)
                return this.format(e, n);
            var s = a;
            !(s > 1) || "numeric" !== o.year && "2-digit" !== o.year || "numeric" !== o.month && "2-digit" !== o.month || "numeric" !== o.day && "2-digit" !== o.day || (s = 1);
            var l = this.format(e, n)
              , u = this.format(t, n);
            if (l === u)
                return l;
            var c = Cn(function(e, t) {
                var n = {};
                for (var r in e)
                    (!(r in vn) || vn[r] <= t) && (n[r] = e[r]);
                return n
            }(o, s), i, n)
              , d = c(e)
              , p = c(t)
              , f = function(e, t, n, r) {
                var o = 0;
                for (; o < e.length; ) {
                    var i = e.indexOf(t, o);
                    if (-1 === i)
                        break;
                    var a = e.substr(0, i);
                    o = i + t.length;
                    for (var s = e.substr(o), l = 0; l < n.length; ) {
                        var u = n.indexOf(r, l);
                        if (-1 === u)
                            break;
                        var c = n.substr(0, u);
                        l = u + r.length;
                        var d = n.substr(l);
                        if (a === c && s === d)
                            return {
                                before: a,
                                after: s
                            }
                    }
                }
                return null
            }(l, d, u, p)
              , h = i.separator || r || n.defaultSeparator || "";
            return f ? f.before + d + h + p + f.after : l + h + u
        }
        ,
        e.prototype.getLargestUnit = function() {
            switch (this.severity) {
            case 7:
            case 6:
            case 5:
                return "year";
            case 4:
                return "month";
            case 3:
                return "week";
            case 2:
                return "day";
            default:
                return "time"
            }
        }
        ,
        e
    }();
    function Cn(e, t, n) {
        var o = Object.keys(e).length;
        return 1 === o && "short" === e.timeZoneName ? function(e) {
            return sn(e.timeZoneOffset)
        }
        : 0 === o && t.week ? function(e) {
            return function(e, t, n, r, o) {
                var i = [];
                "long" === o ? i.push(n) : "short" !== o && "narrow" !== o || i.push(t);
                "long" !== o && "short" !== o || i.push(" ");
                i.push(r.simpleNumberFormat.format(e)),
                "rtl" === r.options.direction && i.reverse();
                return i.join("")
            }(n.computeWeekNumber(e.marker), n.weekText, n.weekTextLong, n.locale, t.week)
        }
        : function(e, t, n) {
            e = r({}, e),
            t = r({}, t),
            function(e, t) {
                e.timeZoneName && (e.hour || (e.hour = "2-digit"),
                e.minute || (e.minute = "2-digit"));
                "long" === e.timeZoneName && (e.timeZoneName = "short");
                t.omitZeroMinute && (e.second || e.millisecond) && delete t.omitZeroMinute
            }(e, t),
            e.timeZone = "UTC";
            var o, i = new Intl.DateTimeFormat(n.locale.codes,e);
            if (t.omitZeroMinute) {
                var a = r({}, e);
                delete a.minute,
                o = new Intl.DateTimeFormat(n.locale.codes,a)
            }
            return function(r) {
                var a = r.marker;
                return function(e, t, n, r, o) {
                    e = e.replace(Sn, ""),
                    "short" === n.timeZoneName && (e = function(e, t) {
                        var n = !1;
                        e = e.replace(En, (function() {
                            return n = !0,
                            t
                        }
                        )),
                        n || (e += " " + t);
                        return e
                    }(e, "UTC" === o.timeZone || null == t.timeZoneOffset ? "UTC" : sn(t.timeZoneOffset)));
                    r.omitCommas && (e = e.replace(mn, "").trim());
                    r.omitZeroMinute && (e = e.replace(":00", ""));
                    !1 === r.meridiem ? e = e.replace(gn, "").trim() : "narrow" === r.meridiem ? e = e.replace(gn, (function(e, t) {
                        return t.toLocaleLowerCase()
                    }
                    )) : "short" === r.meridiem ? e = e.replace(gn, (function(e, t) {
                        return t.toLocaleLowerCase() + "m"
                    }
                    )) : "lowercase" === r.meridiem && (e = e.replace(gn, (function(e) {
                        return e.toLocaleLowerCase()
                    }
                    )));
                    return e = (e = e.replace(yn, " ")).trim()
                }((o && !a.getUTCMinutes() ? o : i).format(a), r, e, t, n)
            }
        }(e, t, n)
    }
    function Dn(e, t) {
        var n = t.markerToArray(e.marker);
        return {
            marker: e.marker,
            timeZoneOffset: e.timeZoneOffset,
            array: n,
            year: n[0],
            month: n[1],
            day: n[2],
            hour: n[3],
            minute: n[4],
            second: n[5],
            millisecond: n[6]
        }
    }
    function Rn(e, t, n, r) {
        var o = Dn(e, n.calendarSystem);
        return {
            date: o,
            start: o,
            end: t ? Dn(t, n.calendarSystem) : null,
            timeZone: n.timeZone,
            localeCodes: n.locale.codes,
            defaultSeparator: r || n.defaultSeparator
        }
    }
    var wn = function() {
        function e(e) {
            this.cmdStr = e
        }
        return e.prototype.format = function(e, t, n) {
            return t.cmdFormatter(this.cmdStr, Rn(e, null, t, n))
        }
        ,
        e.prototype.formatRange = function(e, t, n, r) {
            return n.cmdFormatter(this.cmdStr, Rn(e, t, n, r))
        }
        ,
        e
    }()
      , Tn = function() {
        function e(e) {
            this.func = e
        }
        return e.prototype.format = function(e, t, n) {
            return this.func(Rn(e, null, t, n))
        }
        ,
        e.prototype.formatRange = function(e, t, n, r) {
            return this.func(Rn(e, t, n, r))
        }
        ,
        e
    }();
    function _n(e) {
        return "object" == typeof e && e ? new bn(e) : "string" == typeof e ? new wn(e) : "function" == typeof e ? new Tn(e) : null
    }
    var xn = {
        navLinkDayClick: Wn,
        navLinkWeekClick: Wn,
        duration: qt,
        bootstrapFontAwesome: Wn,
        buttonIcons: Wn,
        customButtons: Wn,
        defaultAllDayEventDuration: qt,
        defaultTimedEventDuration: qt,
        nextDayThreshold: qt,
        scrollTime: qt,
        scrollTimeReset: Boolean,
        slotMinTime: qt,
        slotMaxTime: qt,
        dayPopoverFormat: _n,
        slotDuration: qt,
        snapDuration: qt,
        headerToolbar: Wn,
        footerToolbar: Wn,
        defaultRangeSeparator: String,
        titleRangeSeparator: String,
        forceEventDuration: Boolean,
        dayHeaders: Boolean,
        dayHeaderFormat: _n,
        dayHeaderClassNames: Wn,
        dayHeaderContent: Wn,
        dayHeaderDidMount: Wn,
        dayHeaderWillUnmount: Wn,
        dayCellClassNames: Wn,
        dayCellContent: Wn,
        dayCellDidMount: Wn,
        dayCellWillUnmount: Wn,
        initialView: String,
        aspectRatio: Number,
        weekends: Boolean,
        weekNumberCalculation: Wn,
        weekNumbers: Boolean,
        weekNumberClassNames: Wn,
        weekNumberContent: Wn,
        weekNumberDidMount: Wn,
        weekNumberWillUnmount: Wn,
        editable: Boolean,
        viewClassNames: Wn,
        viewDidMount: Wn,
        viewWillUnmount: Wn,
        nowIndicator: Boolean,
        nowIndicatorClassNames: Wn,
        nowIndicatorContent: Wn,
        nowIndicatorDidMount: Wn,
        nowIndicatorWillUnmount: Wn,
        showNonCurrentDates: Boolean,
        lazyFetching: Boolean,
        startParam: String,
        endParam: String,
        timeZoneParam: String,
        timeZone: String,
        locales: Wn,
        locale: Wn,
        themeSystem: String,
        dragRevertDuration: Number,
        dragScroll: Boolean,
        allDayMaintainDuration: Boolean,
        unselectAuto: Boolean,
        dropAccept: Wn,
        eventOrder: rt,
        eventOrderStrict: Boolean,
        handleWindowResize: Boolean,
        windowResizeDelay: Number,
        longPressDelay: Number,
        eventDragMinDistance: Number,
        expandRows: Boolean,
        height: Wn,
        contentHeight: Wn,
        direction: String,
        weekNumberFormat: _n,
        eventResizableFromStart: Boolean,
        displayEventTime: Boolean,
        displayEventEnd: Boolean,
        weekText: String,
        weekTextLong: String,
        progressiveEventRendering: Boolean,
        businessHours: Wn,
        initialDate: Wn,
        now: Wn,
        eventDataTransform: Wn,
        stickyHeaderDates: Wn,
        stickyFooterScrollbar: Wn,
        viewHeight: Wn,
        defaultAllDay: Boolean,
        eventSourceFailure: Wn,
        eventSourceSuccess: Wn,
        eventDisplay: String,
        eventStartEditable: Boolean,
        eventDurationEditable: Boolean,
        eventOverlap: Wn,
        eventConstraint: Wn,
        eventAllow: Wn,
        eventBackgroundColor: String,
        eventBorderColor: String,
        eventTextColor: String,
        eventColor: String,
        eventClassNames: Wn,
        eventContent: Wn,
        eventDidMount: Wn,
        eventWillUnmount: Wn,
        selectConstraint: Wn,
        selectOverlap: Wn,
        selectAllow: Wn,
        droppable: Boolean,
        unselectCancel: String,
        slotLabelFormat: Wn,
        slotLaneClassNames: Wn,
        slotLaneContent: Wn,
        slotLaneDidMount: Wn,
        slotLaneWillUnmount: Wn,
        slotLabelClassNames: Wn,
        slotLabelContent: Wn,
        slotLabelDidMount: Wn,
        slotLabelWillUnmount: Wn,
        dayMaxEvents: Wn,
        dayMaxEventRows: Wn,
        dayMinWidth: Number,
        slotLabelInterval: qt,
        allDayText: String,
        allDayClassNames: Wn,
        allDayContent: Wn,
        allDayDidMount: Wn,
        allDayWillUnmount: Wn,
        slotMinWidth: Number,
        navLinks: Boolean,
        eventTimeFormat: _n,
        rerenderDelay: Number,
        moreLinkText: Wn,
        moreLinkHint: Wn,
        selectMinDistance: Number,
        selectable: Boolean,
        selectLongPressDelay: Number,
        eventLongPressDelay: Number,
        selectMirror: Boolean,
        eventMaxStack: Number,
        eventMinHeight: Number,
        eventMinWidth: Number,
        eventShortHeight: Number,
        slotEventOverlap: Boolean,
        plugins: Wn,
        firstDay: Number,
        dayCount: Number,
        dateAlignment: String,
        dateIncrement: qt,
        hiddenDays: Wn,
        monthMode: Boolean,
        fixedWeekCount: Boolean,
        validRange: Wn,
        visibleRange: Wn,
        titleFormat: Wn,
        eventInteractive: Boolean,
        noEventsText: String,
        viewHint: Wn,
        navLinkHint: Wn,
        closeHint: String,
        timeHint: String,
        eventHint: String,
        moreLinkClick: Wn,
        moreLinkClassNames: Wn,
        moreLinkContent: Wn,
        moreLinkDidMount: Wn,
        moreLinkWillUnmount: Wn
    }
      , kn = {
        eventDisplay: "auto",
        defaultRangeSeparator: " - ",
        titleRangeSeparator: " – ",
        defaultTimedEventDuration: "01:00:00",
        defaultAllDayEventDuration: {
            day: 1
        },
        forceEventDuration: !1,
        nextDayThreshold: "00:00:00",
        dayHeaders: !0,
        initialView: "",
        aspectRatio: 1.35,
        headerToolbar: {
            start: "title",
            center: "",
            end: "today prev,next"
        },
        weekends: !0,
        weekNumbers: !1,
        weekNumberCalculation: "local",
        editable: !1,
        nowIndicator: !1,
        scrollTime: "06:00:00",
        scrollTimeReset: !0,
        slotMinTime: "00:00:00",
        slotMaxTime: "24:00:00",
        showNonCurrentDates: !0,
        lazyFetching: !0,
        startParam: "start",
        endParam: "end",
        timeZoneParam: "timeZone",
        timeZone: "local",
        locales: [],
        locale: "",
        themeSystem: "standard",
        dragRevertDuration: 500,
        dragScroll: !0,
        allDayMaintainDuration: !1,
        unselectAuto: !0,
        dropAccept: "*",
        eventOrder: "start,-duration,allDay,title",
        dayPopoverFormat: {
            month: "long",
            day: "numeric",
            year: "numeric"
        },
        handleWindowResize: !0,
        windowResizeDelay: 100,
        longPressDelay: 1e3,
        eventDragMinDistance: 5,
        expandRows: !1,
        navLinks: !1,
        selectable: !1,
        eventMinHeight: 15,
        eventMinWidth: 30,
        eventShortHeight: 30
    }
      , Mn = {
        datesSet: Wn,
        eventsSet: Wn,
        eventAdd: Wn,
        eventChange: Wn,
        eventRemove: Wn,
        windowResize: Wn,
        eventClick: Wn,
        eventMouseEnter: Wn,
        eventMouseLeave: Wn,
        select: Wn,
        unselect: Wn,
        loading: Wn,
        _unmount: Wn,
        _beforeprint: Wn,
        _afterprint: Wn,
        _noEventDrop: Wn,
        _noEventResize: Wn,
        _resize: Wn,
        _scrollRequest: Wn
    }
      , In = {
        buttonText: Wn,
        buttonHints: Wn,
        views: Wn,
        plugins: Wn,
        initialEvents: Wn,
        events: Wn,
        eventSources: Wn
    }
      , Pn = {
        headerToolbar: Nn,
        footerToolbar: Nn,
        buttonText: Nn,
        buttonHints: Nn,
        buttonIcons: Nn,
        dateIncrement: Nn
    };
    function Nn(e, t) {
        return "object" == typeof e && "object" == typeof t && e && t ? Wt(e, t) : e === t
    }
    var Hn = {
        type: String,
        component: Wn,
        buttonText: String,
        buttonTextKey: String,
        dateProfileGeneratorClass: Wn,
        usesMinMaxTime: Boolean,
        classNames: Wn,
        content: Wn,
        didMount: Wn,
        willUnmount: Wn
    };
    function On(e) {
        return Pt(e, Pn)
    }
    function An(e, t) {
        var n = {}
          , r = {};
        for (var o in t)
            o in e && (n[o] = t[o](e[o]));
        for (var o in e)
            o in t || (r[o] = e[o]);
        return {
            refined: n,
            extra: r
        }
    }
    function Wn(e) {
        return e
    }
    function Ln(e, t, n, r) {
        for (var o = {
            defs: {},
            instances: {}
        }, i = tr(n), a = 0, s = e; a < s.length; a++) {
            var l = Qn(s[a], t, n, r, i);
            l && Un(l, o)
        }
        return o
    }
    function Un(e, t) {
        return void 0 === t && (t = {
            defs: {},
            instances: {}
        }),
        t.defs[e.def.defId] = e.def,
        e.instance && (t.instances[e.instance.instanceId] = e.instance),
        t
    }
    function Bn(e, t) {
        var n = e.instances[t];
        if (n) {
            var r = e.defs[n.defId]
              , o = Fn(e, (function(e) {
                return t = r,
                n = e,
                Boolean(t.groupId && t.groupId === n.groupId);
                var t, n
            }
            ));
            return o.defs[r.defId] = r,
            o.instances[n.instanceId] = n,
            o
        }
        return {
            defs: {},
            instances: {}
        }
    }
    function zn() {
        return {
            defs: {},
            instances: {}
        }
    }
    function Vn(e, t) {
        return {
            defs: r(r({}, e.defs), t.defs),
            instances: r(r({}, e.instances), t.instances)
        }
    }
    function Fn(e, t) {
        var n = Nt(e.defs, t)
          , r = Nt(e.instances, (function(e) {
            return n[e.defId]
        }
        ));
        return {
            defs: n,
            instances: r
        }
    }
    function Gn(e) {
        return Array.isArray(e) ? e : "string" == typeof e ? e.split(/\s+/) : []
    }
    var jn = {
        display: String,
        editable: Boolean,
        startEditable: Boolean,
        durationEditable: Boolean,
        constraint: Wn,
        overlap: Wn,
        allow: Wn,
        className: Gn,
        classNames: Gn,
        color: String,
        backgroundColor: String,
        borderColor: String,
        textColor: String
    }
      , qn = {
        display: null,
        startEditable: null,
        durationEditable: null,
        constraints: [],
        overlap: null,
        allows: [],
        backgroundColor: "",
        borderColor: "",
        textColor: "",
        classNames: []
    };
    function Yn(e, t) {
        var n = function(e, t) {
            return Array.isArray(e) ? Ln(e, null, t, !0) : "object" == typeof e && e ? Ln([e], null, t, !0) : null != e ? String(e) : null
        }(e.constraint, t);
        return {
            display: e.display || null,
            startEditable: null != e.startEditable ? e.startEditable : e.editable,
            durationEditable: null != e.durationEditable ? e.durationEditable : e.editable,
            constraints: null != n ? [n] : [],
            overlap: null != e.overlap ? e.overlap : null,
            allows: null != e.allow ? [e.allow] : [],
            backgroundColor: e.backgroundColor || e.color || "",
            borderColor: e.borderColor || e.color || "",
            textColor: e.textColor || "",
            classNames: (e.className || []).concat(e.classNames || [])
        }
    }
    function Zn(e) {
        return e.reduce(Xn, qn)
    }
    function Xn(e, t) {
        return {
            display: null != t.display ? t.display : e.display,
            startEditable: null != t.startEditable ? t.startEditable : e.startEditable,
            durationEditable: null != t.durationEditable ? t.durationEditable : e.durationEditable,
            constraints: e.constraints.concat(t.constraints),
            overlap: "boolean" == typeof t.overlap ? t.overlap : e.overlap,
            allows: e.allows.concat(t.allows),
            backgroundColor: t.backgroundColor || e.backgroundColor,
            borderColor: t.borderColor || e.borderColor,
            textColor: t.textColor || e.textColor,
            classNames: e.classNames.concat(t.classNames)
        }
    }
    var Kn = {
        id: String,
        groupId: String,
        title: String,
        url: String,
        interactive: Boolean
    }
      , $n = {
        start: Wn,
        end: Wn,
        date: Wn,
        allDay: Boolean
    }
      , Jn = r(r(r({}, Kn), $n), {
        extendedProps: Wn
    });
    function Qn(e, t, n, r, o) {
        void 0 === o && (o = tr(n));
        var i = er(e, n, o)
          , a = i.refined
          , s = i.extra
          , l = function(e, t) {
            var n = null;
            e && (n = e.defaultAllDay);
            null == n && (n = t.options.defaultAllDay);
            return n
        }(t, n)
          , u = function(e, t, n, r) {
            for (var o = 0; o < r.length; o += 1) {
                var i = r[o].parse(e, n);
                if (i) {
                    var a = e.allDay;
                    return null == a && null == (a = t) && null == (a = i.allDayGuess) && (a = !1),
                    {
                        allDay: a,
                        duration: i.duration,
                        typeData: i.typeData,
                        typeId: o
                    }
                }
            }
            return null
        }(a, l, n.dateEnv, n.pluginHooks.recurringTypes);
        if (u)
            return (c = nr(a, s, t ? t.sourceId : "", u.allDay, Boolean(u.duration), n)).recurringDef = {
                typeId: u.typeId,
                typeData: u.typeData,
                duration: u.duration
            },
            {
                def: c,
                instance: null
            };
        var c, d = function(e, t, n, r) {
            var o, i, a = e.allDay, s = null, l = !1, u = null, c = null != e.start ? e.start : e.date;
            if (o = n.dateEnv.createMarkerMeta(c))
                s = o.marker;
            else if (!r)
                return null;
            null != e.end && (i = n.dateEnv.createMarkerMeta(e.end));
            null == a && (a = null != t ? t : (!o || o.isTimeUnspecified) && (!i || i.isTimeUnspecified));
            a && s && (s = bt(s));
            i && (u = i.marker,
            a && (u = bt(u)),
            s && u <= s && (u = null));
            u ? l = !0 : r || (l = n.options.forceEventDuration || !1,
            u = n.dateEnv.add(s, a ? n.options.defaultAllDayEventDuration : n.options.defaultTimedEventDuration));
            return {
                allDay: a,
                hasEnd: l,
                range: {
                    start: s,
                    end: u
                },
                forcedStartTzo: o ? o.forcedTzo : null,
                forcedEndTzo: i ? i.forcedTzo : null
            }
        }(a, l, n, r);
        return d ? {
            def: c = nr(a, s, t ? t.sourceId : "", d.allDay, d.hasEnd, n),
            instance: Mt(c.defId, d.range, d.forcedStartTzo, d.forcedEndTzo)
        } : null
    }
    function er(e, t, n) {
        return void 0 === n && (n = tr(t)),
        An(e, n)
    }
    function tr(e) {
        return r(r(r({}, jn), Jn), e.pluginHooks.eventRefiners)
    }
    function nr(e, t, n, o, i, a) {
        for (var s = {
            title: e.title || "",
            groupId: e.groupId || "",
            publicId: e.id || "",
            url: e.url || "",
            recurringDef: null,
            defId: Ke(),
            sourceId: n,
            allDay: o,
            hasEnd: i,
            interactive: e.interactive,
            ui: Yn(e, a),
            extendedProps: r(r({}, e.extendedProps || {}), t)
        }, l = 0, u = a.pluginHooks.eventDefMemberAdders; l < u.length; l++) {
            var c = u[l];
            r(s, c(e))
        }
        return Object.freeze(s.ui.classNames),
        Object.freeze(s.extendedProps),
        s
    }
    function rr(e) {
        var t = Math.floor(mt(e.start, e.end)) || 1
          , n = bt(e.start);
        return {
            start: n,
            end: ht(n, t)
        }
    }
    function or(e, t) {
        void 0 === t && (t = qt(0));
        var n = null
          , r = null;
        if (e.end) {
            r = bt(e.end);
            var o = e.end.valueOf() - r.valueOf();
            o && o >= en(t) && (r = ht(r, 1))
        }
        return e.start && (n = bt(e.start),
        r && r <= n && (r = ht(n, 1))),
        {
            start: n,
            end: r
        }
    }
    function ir(e) {
        var t = or(e);
        return mt(t.start, t.end) > 1
    }
    function ar(e, t, n, r) {
        return "year" === r ? qt(n.diffWholeYears(e, t), "year") : "month" === r ? qt(n.diffWholeMonths(e, t), "month") : yt(e, t)
    }
    function sr(e, t) {
        var n, r, o = [], i = t.start;
        for (e.sort(lr),
        n = 0; n < e.length; n += 1)
            (r = e[n]).start > i && o.push({
                start: i,
                end: r.start
            }),
            r.end > i && (i = r.end);
        return i < t.end && o.push({
            start: i,
            end: t.end
        }),
        o
    }
    function lr(e, t) {
        return e.start.valueOf() - t.start.valueOf()
    }
    function ur(e, t) {
        var n = e.start
          , r = e.end
          , o = null;
        return null !== t.start && (n = null === n ? t.start : new Date(Math.max(n.valueOf(), t.start.valueOf()))),
        null != t.end && (r = null === r ? t.end : new Date(Math.min(r.valueOf(), t.end.valueOf()))),
        (null === n || null === r || n < r) && (o = {
            start: n,
            end: r
        }),
        o
    }
    function cr(e, t) {
        return (null === e.start ? null : e.start.valueOf()) === (null === t.start ? null : t.start.valueOf()) && (null === e.end ? null : e.end.valueOf()) === (null === t.end ? null : t.end.valueOf())
    }
    function dr(e, t) {
        return (null === e.end || null === t.start || e.end > t.start) && (null === e.start || null === t.end || e.start < t.end)
    }
    function pr(e, t) {
        return (null === e.start || null !== t.start && t.start >= e.start) && (null === e.end || null !== t.end && t.end <= e.end)
    }
    function fr(e, t) {
        return (null === e.start || t >= e.start) && (null === e.end || t < e.end)
    }
    function hr(e, t, n, r) {
        var o = {}
          , i = {}
          , a = {}
          , s = []
          , l = []
          , u = yr(e.defs, t);
        for (var c in e.defs) {
            "inverse-background" === (f = u[(E = e.defs[c]).defId]).display && (E.groupId ? (o[E.groupId] = [],
            a[E.groupId] || (a[E.groupId] = E)) : i[c] = [])
        }
        for (var d in e.instances) {
            var p = e.instances[d]
              , f = u[(E = e.defs[p.defId]).defId]
              , h = p.range
              , v = !E.allDay && r ? or(h, r) : h
              , g = ur(v, n);
            g && ("inverse-background" === f.display ? E.groupId ? o[E.groupId].push(g) : i[p.defId].push(g) : "none" !== f.display && ("background" === f.display ? s : l).push({
                def: E,
                ui: f,
                instance: p,
                range: g,
                isStart: v.start && v.start.valueOf() === g.start.valueOf(),
                isEnd: v.end && v.end.valueOf() === g.end.valueOf()
            }))
        }
        for (var m in o)
            for (var y = 0, S = sr(o[m], n); y < S.length; y++) {
                var E, b = S[y];
                f = u[(E = a[m]).defId];
                s.push({
                    def: E,
                    ui: f,
                    instance: null,
                    range: b,
                    isStart: !1,
                    isEnd: !1
                })
            }
        for (var c in i)
            for (var C = 0, D = sr(i[c], n); C < D.length; C++) {
                b = D[C];
                s.push({
                    def: e.defs[c],
                    ui: u[c],
                    instance: null,
                    range: b,
                    isStart: !1,
                    isEnd: !1
                })
            }
        return {
            bg: s,
            fg: l
        }
    }
    function vr(e) {
        return "background" === e.ui.display || "inverse-background" === e.ui.display
    }
    function gr(e, t) {
        e.fcSeg = t
    }
    function mr(e) {
        return e.fcSeg || e.parentNode.fcSeg || null
    }
    function yr(e, t) {
        return Ht(e, (function(e) {
            return Sr(e, t)
        }
        ))
    }
    function Sr(e, t) {
        var n = [];
        return t[""] && n.push(t[""]),
        t[e.defId] && n.push(t[e.defId]),
        n.push(e.ui),
        Zn(n)
    }
    function Er(e, t) {
        var n = e.map(br);
        return n.sort((function(e, n) {
            return ot(e, n, t)
        }
        )),
        n.map((function(e) {
            return e._seg
        }
        ))
    }
    function br(e) {
        var t = e.eventRange
          , n = t.def
          , o = t.instance ? t.instance.range : t.range
          , i = o.start ? o.start.valueOf() : 0
          , a = o.end ? o.end.valueOf() : 0;
        return r(r(r({}, n.extendedProps), n), {
            id: n.publicId,
            start: i,
            end: a,
            duration: a - i,
            allDay: Number(n.allDay),
            _seg: e
        })
    }
    function Cr(e, t) {
        for (var n = t.pluginHooks.isDraggableTransformers, r = e.eventRange, o = r.def, i = r.ui, a = i.startEditable, s = 0, l = n; s < l.length; s++) {
            a = (0,
            l[s])(a, o, i, t)
        }
        return a
    }
    function Dr(e, t) {
        return e.isStart && e.eventRange.ui.durationEditable && t.options.eventResizableFromStart
    }
    function Rr(e, t) {
        return e.isEnd && e.eventRange.ui.durationEditable
    }
    function wr(e, t, n, r, o, i, a) {
        var s = n.dateEnv
          , l = n.options
          , u = l.displayEventTime
          , c = l.displayEventEnd
          , d = e.eventRange.def
          , p = e.eventRange.instance;
        null == u && (u = !1 !== r),
        null == c && (c = !1 !== o);
        var f = p.range.start
          , h = p.range.end
          , v = i || e.start || e.eventRange.range.start
          , g = a || e.end || e.eventRange.range.end
          , m = bt(f).valueOf() === bt(v).valueOf()
          , y = bt(vt(h, -1)).valueOf() === bt(vt(g, -1)).valueOf();
        return u && !d.allDay && (m || y) ? (v = m ? f : v,
        g = y ? h : g,
        c && d.hasEnd ? s.formatRange(v, g, t, {
            forcedStartTzo: i ? null : p.forcedStartTzo,
            forcedEndTzo: a ? null : p.forcedEndTzo
        }) : s.format(v, t, {
            forcedTzo: i ? null : p.forcedStartTzo
        })) : ""
    }
    function Tr(e, t, n) {
        var r = e.eventRange.range;
        return {
            isPast: r.end < (n || t.start),
            isFuture: r.start >= (n || t.end),
            isToday: t && fr(t, r.start)
        }
    }
    function _r(e) {
        var t = ["fc-event"];
        return e.isMirror && t.push("fc-event-mirror"),
        e.isDraggable && t.push("fc-event-draggable"),
        (e.isStartResizable || e.isEndResizable) && t.push("fc-event-resizable"),
        e.isDragging && t.push("fc-event-dragging"),
        e.isResizing && t.push("fc-event-resizing"),
        e.isSelected && t.push("fc-event-selected"),
        e.isStart && t.push("fc-event-start"),
        e.isEnd && t.push("fc-event-end"),
        e.isPast && t.push("fc-event-past"),
        e.isToday && t.push("fc-event-today"),
        e.isFuture && t.push("fc-event-future"),
        t
    }
    function xr(e) {
        return e.instance ? e.instance.instanceId : e.def.defId + ":" + e.range.start.toISOString()
    }
    function kr(e, t) {
        var n = e.eventRange
          , r = n.def
          , o = n.instance
          , i = r.url;
        if (i)
            return {
                href: i
            };
        var a = t.emitter
          , s = t.options.eventInteractive;
        return null == s && null == (s = r.interactive) && (s = Boolean(a.hasHandlers("eventClick"))),
        s ? Ze((function(e) {
            a.trigger("eventClick", {
                el: e.target,
                event: new Zr(t,r,o),
                jsEvent: e,
                view: t.viewApi
            })
        }
        )) : {}
    }
    var Mr = {
        start: Wn,
        end: Wn,
        allDay: Boolean
    };
    function Ir(e, t, n) {
        var o = function(e, t) {
            var n = An(e, Mr)
              , o = n.refined
              , i = n.extra
              , a = o.start ? t.createMarkerMeta(o.start) : null
              , s = o.end ? t.createMarkerMeta(o.end) : null
              , l = o.allDay;
            null == l && (l = a && a.isTimeUnspecified && (!s || s.isTimeUnspecified));
            return r({
                range: {
                    start: a ? a.marker : null,
                    end: s ? s.marker : null
                },
                allDay: l
            }, i)
        }(e, t)
          , i = o.range;
        if (!i.start)
            return null;
        if (!i.end) {
            if (null == n)
                return null;
            i.end = t.add(i.start, n)
        }
        return o
    }
    function Pr(e, t) {
        return cr(e.range, t.range) && e.allDay === t.allDay && function(e, t) {
            for (var n in t)
                if ("range" !== n && "allDay" !== n && e[n] !== t[n])
                    return !1;
            for (var n in e)
                if (!(n in t))
                    return !1;
            return !0
        }(e, t)
    }
    function Nr(e, t, n) {
        return r(r({}, Hr(e, t, n)), {
            timeZone: t.timeZone
        })
    }
    function Hr(e, t, n) {
        return {
            start: t.toDate(e.start),
            end: t.toDate(e.end),
            startStr: t.formatIso(e.start, {
                omitTime: n
            }),
            endStr: t.formatIso(e.end, {
                omitTime: n
            })
        }
    }
    function Or(e, t, n) {
        var r = er({
            editable: !1
        }, n)
          , o = nr(r.refined, r.extra, "", e.allDay, !0, n);
        return {
            def: o,
            ui: Sr(o, t),
            instance: Mt(o.defId, e.range),
            range: e.range,
            isStart: !0,
            isEnd: !0
        }
    }
    function Ar(e, t, n) {
        n.emitter.trigger("select", r(r({}, Wr(e, n)), {
            jsEvent: t ? t.origEvent : null,
            view: n.viewApi || n.calendarApi.view
        }))
    }
    function Wr(e, t) {
        for (var n, o, i = {}, a = 0, s = t.pluginHooks.dateSpanTransforms; a < s.length; a++) {
            var l = s[a];
            r(i, l(e, t))
        }
        return r(i, (n = e,
        o = t.dateEnv,
        r(r({}, Hr(n.range, o, n.allDay)), {
            allDay: n.allDay
        }))),
        i
    }
    function Lr(e, t, n) {
        var r = n.dateEnv
          , o = n.options
          , i = t;
        return e ? (i = bt(i),
        i = r.add(i, o.defaultAllDayEventDuration)) : i = r.add(i, o.defaultTimedEventDuration),
        i
    }
    function Ur(e, t, n, r) {
        var o = yr(e.defs, t)
          , i = {
            defs: {},
            instances: {}
        };
        for (var a in e.defs) {
            var s = e.defs[a];
            i.defs[a] = Br(s, o[a], n, r)
        }
        for (var l in e.instances) {
            var u = e.instances[l];
            s = i.defs[u.defId];
            i.instances[l] = zr(u, s, o[u.defId], n, r)
        }
        return i
    }
    function Br(e, t, n, o) {
        var i = n.standardProps || {};
        null == i.hasEnd && t.durationEditable && (n.startDelta || n.endDelta) && (i.hasEnd = !0);
        var a = r(r(r({}, e), i), {
            ui: r(r({}, e.ui), i.ui)
        });
        n.extendedProps && (a.extendedProps = r(r({}, a.extendedProps), n.extendedProps));
        for (var s = 0, l = o.pluginHooks.eventDefMutationAppliers; s < l.length; s++) {
            (0,
            l[s])(a, n, o)
        }
        return !a.hasEnd && o.options.forceEventDuration && (a.hasEnd = !0),
        a
    }
    function zr(e, t, n, o, i) {
        var a = i.dateEnv
          , s = o.standardProps && !0 === o.standardProps.allDay
          , l = o.standardProps && !1 === o.standardProps.hasEnd
          , u = r({}, e);
        return s && (u.range = rr(u.range)),
        o.datesDelta && n.startEditable && (u.range = {
            start: a.add(u.range.start, o.datesDelta),
            end: a.add(u.range.end, o.datesDelta)
        }),
        o.startDelta && n.durationEditable && (u.range = {
            start: a.add(u.range.start, o.startDelta),
            end: u.range.end
        }),
        o.endDelta && n.durationEditable && (u.range = {
            start: u.range.start,
            end: a.add(u.range.end, o.endDelta)
        }),
        l && (u.range = {
            start: u.range.start,
            end: Lr(t.allDay, u.range.start, i)
        }),
        t.allDay && (u.range = {
            start: bt(u.range.start),
            end: bt(u.range.end)
        }),
        u.range.end < u.range.start && (u.range.end = Lr(t.allDay, u.range.start, i)),
        u
    }
    var Vr = function() {
        function e(e, t, n) {
            this.type = e,
            this.getCurrentData = t,
            this.dateEnv = n
        }
        return Object.defineProperty(e.prototype, "calendar", {
            get: function() {
                return this.getCurrentData().calendarApi
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "title", {
            get: function() {
                return this.getCurrentData().viewTitle
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "activeStart", {
            get: function() {
                return this.dateEnv.toDate(this.getCurrentData().dateProfile.activeRange.start)
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "activeEnd", {
            get: function() {
                return this.dateEnv.toDate(this.getCurrentData().dateProfile.activeRange.end)
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "currentStart", {
            get: function() {
                return this.dateEnv.toDate(this.getCurrentData().dateProfile.currentRange.start)
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "currentEnd", {
            get: function() {
                return this.dateEnv.toDate(this.getCurrentData().dateProfile.currentRange.end)
            },
            enumerable: !1,
            configurable: !0
        }),
        e.prototype.getOption = function(e) {
            return this.getCurrentData().options[e]
        }
        ,
        e
    }()
      , Fr = {
        id: String,
        defaultAllDay: Boolean,
        url: String,
        format: String,
        events: Wn,
        eventDataTransform: Wn,
        success: Wn,
        failure: Wn
    };
    function Gr(e, t, n) {
        var r;
        if (void 0 === n && (n = jr(t)),
        "string" == typeof e ? r = {
            url: e
        } : "function" == typeof e || Array.isArray(e) ? r = {
            events: e
        } : "object" == typeof e && e && (r = e),
        r) {
            var o = An(r, n)
              , i = o.refined
              , a = o.extra
              , s = function(e, t) {
                for (var n = t.pluginHooks.eventSourceDefs, r = n.length - 1; r >= 0; r -= 1) {
                    var o = n[r].parseMeta(e);
                    if (o)
                        return {
                            sourceDefId: r,
                            meta: o
                        }
                }
                return null
            }(i, t);
            if (s)
                return {
                    _raw: e,
                    isFetching: !1,
                    latestFetchId: "",
                    fetchRange: null,
                    defaultAllDay: i.defaultAllDay,
                    eventDataTransform: i.eventDataTransform,
                    success: i.success,
                    failure: i.failure,
                    publicId: i.id || "",
                    sourceId: Ke(),
                    sourceDefId: s.sourceDefId,
                    meta: s.meta,
                    ui: Yn(i, t),
                    extendedProps: a
                }
        }
        return null
    }
    function jr(e) {
        return r(r(r({}, jn), Fr), e.pluginHooks.eventSourceRefiners)
    }
    function qr(e, t) {
        return "function" == typeof e && (e = e()),
        null == e ? t.createNowMarker() : t.createMarker(e)
    }
    var Yr = function() {
        function e() {}
        return e.prototype.getCurrentData = function() {
            return this.currentDataManager.getCurrentData()
        }
        ,
        e.prototype.dispatch = function(e) {
            return this.currentDataManager.dispatch(e)
        }
        ,
        Object.defineProperty(e.prototype, "view", {
            get: function() {
                return this.getCurrentData().viewApi
            },
            enumerable: !1,
            configurable: !0
        }),
        e.prototype.batchRendering = function(e) {
            e()
        }
        ,
        e.prototype.updateSize = function() {
            this.trigger("_resize", !0)
        }
        ,
        e.prototype.setOption = function(e, t) {
            this.dispatch({
                type: "SET_OPTION",
                optionName: e,
                rawOptionValue: t
            })
        }
        ,
        e.prototype.getOption = function(e) {
            return this.currentDataManager.currentCalendarOptionsInput[e]
        }
        ,
        e.prototype.getAvailableLocaleCodes = function() {
            return Object.keys(this.getCurrentData().availableRawLocales)
        }
        ,
        e.prototype.on = function(e, t) {
            var n = this.currentDataManager;
            n.currentCalendarOptionsRefiners[e] ? n.emitter.on(e, t) : console.warn("Unknown listener name '" + e + "'")
        }
        ,
        e.prototype.off = function(e, t) {
            this.currentDataManager.emitter.off(e, t)
        }
        ,
        e.prototype.trigger = function(e) {
            for (var t, n = [], r = 1; r < arguments.length; r++)
                n[r - 1] = arguments[r];
            (t = this.currentDataManager.emitter).trigger.apply(t, o([e], n))
        }
        ,
        e.prototype.changeView = function(e, t) {
            var n = this;
            this.batchRendering((function() {
                if (n.unselect(),
                t)
                    if (t.start && t.end)
                        n.dispatch({
                            type: "CHANGE_VIEW_TYPE",
                            viewType: e
                        }),
                        n.dispatch({
                            type: "SET_OPTION",
                            optionName: "visibleRange",
                            rawOptionValue: t
                        });
                    else {
                        var r = n.getCurrentData().dateEnv;
                        n.dispatch({
                            type: "CHANGE_VIEW_TYPE",
                            viewType: e,
                            dateMarker: r.createMarker(t)
                        })
                    }
                else
                    n.dispatch({
                        type: "CHANGE_VIEW_TYPE",
                        viewType: e
                    })
            }
            ))
        }
        ,
        e.prototype.zoomTo = function(e, t) {
            var n;
            t = t || "day",
            n = this.getCurrentData().viewSpecs[t] || this.getUnitViewSpec(t),
            this.unselect(),
            n ? this.dispatch({
                type: "CHANGE_VIEW_TYPE",
                viewType: n.type,
                dateMarker: e
            }) : this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: e
            })
        }
        ,
        e.prototype.getUnitViewSpec = function(e) {
            var t, n, r = this.getCurrentData(), o = r.viewSpecs, i = r.toolbarConfig, a = [].concat(i.header ? i.header.viewsWithButtons : [], i.footer ? i.footer.viewsWithButtons : []);
            for (var s in o)
                a.push(s);
            for (t = 0; t < a.length; t += 1)
                if ((n = o[a[t]]) && n.singleUnit === e)
                    return n;
            return null
        }
        ,
        e.prototype.prev = function() {
            this.unselect(),
            this.dispatch({
                type: "PREV"
            })
        }
        ,
        e.prototype.next = function() {
            this.unselect(),
            this.dispatch({
                type: "NEXT"
            })
        }
        ,
        e.prototype.prevYear = function() {
            var e = this.getCurrentData();
            this.unselect(),
            this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: e.dateEnv.addYears(e.currentDate, -1)
            })
        }
        ,
        e.prototype.nextYear = function() {
            var e = this.getCurrentData();
            this.unselect(),
            this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: e.dateEnv.addYears(e.currentDate, 1)
            })
        }
        ,
        e.prototype.today = function() {
            var e = this.getCurrentData();
            this.unselect(),
            this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: qr(e.calendarOptions.now, e.dateEnv)
            })
        }
        ,
        e.prototype.gotoDate = function(e) {
            var t = this.getCurrentData();
            this.unselect(),
            this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: t.dateEnv.createMarker(e)
            })
        }
        ,
        e.prototype.incrementDate = function(e) {
            var t = this.getCurrentData()
              , n = qt(e);
            n && (this.unselect(),
            this.dispatch({
                type: "CHANGE_DATE",
                dateMarker: t.dateEnv.add(t.currentDate, n)
            }))
        }
        ,
        e.prototype.getDate = function() {
            var e = this.getCurrentData();
            return e.dateEnv.toDate(e.currentDate)
        }
        ,
        e.prototype.formatDate = function(e, t) {
            var n = this.getCurrentData().dateEnv;
            return n.format(n.createMarker(e), _n(t))
        }
        ,
        e.prototype.formatRange = function(e, t, n) {
            var r = this.getCurrentData().dateEnv;
            return r.formatRange(r.createMarker(e), r.createMarker(t), _n(n), n)
        }
        ,
        e.prototype.formatIso = function(e, t) {
            var n = this.getCurrentData().dateEnv;
            return n.formatIso(n.createMarker(e), {
                omitTime: t
            })
        }
        ,
        e.prototype.select = function(e, t) {
            var n;
            n = null == t ? null != e.start ? e : {
                start: e,
                end: null
            } : {
                start: e,
                end: t
            };
            var r = this.getCurrentData()
              , o = Ir(n, r.dateEnv, qt({
                days: 1
            }));
            o && (this.dispatch({
                type: "SELECT_DATES",
                selection: o
            }),
            Ar(o, null, r))
        }
        ,
        e.prototype.unselect = function(e) {
            var t = this.getCurrentData();
            t.dateSelection && (this.dispatch({
                type: "UNSELECT_DATES"
            }),
            function(e, t) {
                t.emitter.trigger("unselect", {
                    jsEvent: e ? e.origEvent : null,
                    view: t.viewApi || t.calendarApi.view
                })
            }(e, t))
        }
        ,
        e.prototype.addEvent = function(e, t) {
            if (e instanceof Zr) {
                var n = e._def
                  , r = e._instance;
                return this.getCurrentData().eventStore.defs[n.defId] || (this.dispatch({
                    type: "ADD_EVENTS",
                    eventStore: Un({
                        def: n,
                        instance: r
                    })
                }),
                this.triggerEventAdd(e)),
                e
            }
            var o, i = this.getCurrentData();
            if (t instanceof Me)
                o = t.internalEventSource;
            else if ("boolean" == typeof t)
                t && (o = At(i.eventSources)[0]);
            else if (null != t) {
                var a = this.getEventSourceById(t);
                if (!a)
                    return console.warn('Could not find an event source with ID "' + t + '"'),
                    null;
                o = a.internalEventSource
            }
            var s = Qn(e, o, i, !1);
            if (s) {
                var l = new Zr(i,s.def,s.def.recurringDef ? null : s.instance);
                return this.dispatch({
                    type: "ADD_EVENTS",
                    eventStore: Un(s)
                }),
                this.triggerEventAdd(l),
                l
            }
            return null
        }
        ,
        e.prototype.triggerEventAdd = function(e) {
            var t = this;
            this.getCurrentData().emitter.trigger("eventAdd", {
                event: e,
                relatedEvents: [],
                revert: function() {
                    t.dispatch({
                        type: "REMOVE_EVENTS",
                        eventStore: Xr(e)
                    })
                }
            })
        }
        ,
        e.prototype.getEventById = function(e) {
            var t = this.getCurrentData()
              , n = t.eventStore
              , r = n.defs
              , o = n.instances;
            for (var i in e = String(e),
            r) {
                var a = r[i];
                if (a.publicId === e) {
                    if (a.recurringDef)
                        return new Zr(t,a,null);
                    for (var s in o) {
                        var l = o[s];
                        if (l.defId === a.defId)
                            return new Zr(t,a,l)
                    }
                }
            }
            return null
        }
        ,
        e.prototype.getEvents = function() {
            var e = this.getCurrentData();
            return Kr(e.eventStore, e)
        }
        ,
        e.prototype.removeAllEvents = function() {
            this.dispatch({
                type: "REMOVE_ALL_EVENTS"
            })
        }
        ,
        e.prototype.getEventSources = function() {
            var e = this.getCurrentData()
              , t = e.eventSources
              , n = [];
            for (var r in t)
                n.push(new Me(e,t[r]));
            return n
        }
        ,
        e.prototype.getEventSourceById = function(e) {
            var t = this.getCurrentData()
              , n = t.eventSources;
            for (var r in e = String(e),
            n)
                if (n[r].publicId === e)
                    return new Me(t,n[r]);
            return null
        }
        ,
        e.prototype.addEventSource = function(e) {
            var t = this.getCurrentData();
            if (e instanceof Me)
                return t.eventSources[e.internalEventSource.sourceId] || this.dispatch({
                    type: "ADD_EVENT_SOURCES",
                    sources: [e.internalEventSource]
                }),
                e;
            var n = Gr(e, t);
            return n ? (this.dispatch({
                type: "ADD_EVENT_SOURCES",
                sources: [n]
            }),
            new Me(t,n)) : null
        }
        ,
        e.prototype.removeAllEventSources = function() {
            this.dispatch({
                type: "REMOVE_ALL_EVENT_SOURCES"
            })
        }
        ,
        e.prototype.refetchEvents = function() {
            this.dispatch({
                type: "FETCH_EVENT_SOURCES",
                isRefetch: !0
            })
        }
        ,
        e.prototype.scrollToTime = function(e) {
            var t = qt(e);
            t && this.trigger("_scrollRequest", {
                time: t
            })
        }
        ,
        e
    }()
      , Zr = function() {
        function e(e, t, n) {
            this._context = e,
            this._def = t,
            this._instance = n || null
        }
        return e.prototype.setProp = function(e, t) {
            var n, r;
            if (e in $n)
                console.warn("Could not set date-related prop 'name'. Use one of the date-related methods instead.");
            else if ("id" === e)
                t = Kn[e](t),
                this.mutate({
                    standardProps: {
                        publicId: t
                    }
                });
            else if (e in Kn)
                t = Kn[e](t),
                this.mutate({
                    standardProps: (n = {},
                    n[e] = t,
                    n)
                });
            else if (e in jn) {
                var o = jn[e](t);
                "color" === e ? o = {
                    backgroundColor: t,
                    borderColor: t
                } : "editable" === e ? o = {
                    startEditable: t,
                    durationEditable: t
                } : ((r = {})[e] = t,
                o = r),
                this.mutate({
                    standardProps: {
                        ui: o
                    }
                })
            } else
                console.warn("Could not set prop '" + e + "'. Use setExtendedProp instead.")
        }
        ,
        e.prototype.setExtendedProp = function(e, t) {
            var n;
            this.mutate({
                extendedProps: (n = {},
                n[e] = t,
                n)
            })
        }
        ,
        e.prototype.setStart = function(e, t) {
            void 0 === t && (t = {});
            var n = this._context.dateEnv
              , r = n.createMarker(e);
            if (r && this._instance) {
                var o = ar(this._instance.range.start, r, n, t.granularity);
                t.maintainDuration ? this.mutate({
                    datesDelta: o
                }) : this.mutate({
                    startDelta: o
                })
            }
        }
        ,
        e.prototype.setEnd = function(e, t) {
            void 0 === t && (t = {});
            var n, r = this._context.dateEnv;
            if ((null == e || (n = r.createMarker(e))) && this._instance)
                if (n) {
                    var o = ar(this._instance.range.end, n, r, t.granularity);
                    this.mutate({
                        endDelta: o
                    })
                } else
                    this.mutate({
                        standardProps: {
                            hasEnd: !1
                        }
                    })
        }
        ,
        e.prototype.setDates = function(e, t, n) {
            void 0 === n && (n = {});
            var r, o, i, a = this._context.dateEnv, s = {
                allDay: n.allDay
            }, l = a.createMarker(e);
            if (l && ((null == t || (r = a.createMarker(t))) && this._instance)) {
                var u = this._instance.range;
                !0 === n.allDay && (u = rr(u));
                var c = ar(u.start, l, a, n.granularity);
                if (r) {
                    var d = ar(u.end, r, a, n.granularity);
                    i = d,
                    (o = c).years === i.years && o.months === i.months && o.days === i.days && o.milliseconds === i.milliseconds ? this.mutate({
                        datesDelta: c,
                        standardProps: s
                    }) : this.mutate({
                        startDelta: c,
                        endDelta: d,
                        standardProps: s
                    })
                } else
                    s.hasEnd = !1,
                    this.mutate({
                        datesDelta: c,
                        standardProps: s
                    })
            }
        }
        ,
        e.prototype.moveStart = function(e) {
            var t = qt(e);
            t && this.mutate({
                startDelta: t
            })
        }
        ,
        e.prototype.moveEnd = function(e) {
            var t = qt(e);
            t && this.mutate({
                endDelta: t
            })
        }
        ,
        e.prototype.moveDates = function(e) {
            var t = qt(e);
            t && this.mutate({
                datesDelta: t
            })
        }
        ,
        e.prototype.setAllDay = function(e, t) {
            void 0 === t && (t = {});
            var n = {
                allDay: e
            }
              , r = t.maintainDuration;
            null == r && (r = this._context.options.allDayMaintainDuration),
            this._def.allDay !== e && (n.hasEnd = r),
            this.mutate({
                standardProps: n
            })
        }
        ,
        e.prototype.formatRange = function(e) {
            var t = this._context.dateEnv
              , n = this._instance
              , r = _n(e);
            return this._def.hasEnd ? t.formatRange(n.range.start, n.range.end, r, {
                forcedStartTzo: n.forcedStartTzo,
                forcedEndTzo: n.forcedEndTzo
            }) : t.format(n.range.start, r, {
                forcedTzo: n.forcedStartTzo
            })
        }
        ,
        e.prototype.mutate = function(t) {
            var n = this._instance;
            if (n) {
                var r = this._def
                  , o = this._context
                  , i = o.getCurrentData().eventStore
                  , a = Bn(i, n.instanceId);
                a = Ur(a, {
                    "": {
                        display: "",
                        startEditable: !0,
                        durationEditable: !0,
                        constraints: [],
                        overlap: null,
                        allows: [],
                        backgroundColor: "",
                        borderColor: "",
                        textColor: "",
                        classNames: []
                    }
                }, t, o);
                var s = new e(o,r,n);
                this._def = a.defs[r.defId],
                this._instance = a.instances[n.instanceId],
                o.dispatch({
                    type: "MERGE_EVENTS",
                    eventStore: a
                }),
                o.emitter.trigger("eventChange", {
                    oldEvent: s,
                    event: this,
                    relatedEvents: Kr(a, o, n),
                    revert: function() {
                        o.dispatch({
                            type: "RESET_EVENTS",
                            eventStore: i
                        })
                    }
                })
            }
        }
        ,
        e.prototype.remove = function() {
            var e = this._context
              , t = Xr(this);
            e.dispatch({
                type: "REMOVE_EVENTS",
                eventStore: t
            }),
            e.emitter.trigger("eventRemove", {
                event: this,
                relatedEvents: [],
                revert: function() {
                    e.dispatch({
                        type: "MERGE_EVENTS",
                        eventStore: t
                    })
                }
            })
        }
        ,
        Object.defineProperty(e.prototype, "source", {
            get: function() {
                var e = this._def.sourceId;
                return e ? new Me(this._context,this._context.getCurrentData().eventSources[e]) : null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "start", {
            get: function() {
                return this._instance ? this._context.dateEnv.toDate(this._instance.range.start) : null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "end", {
            get: function() {
                return this._instance && this._def.hasEnd ? this._context.dateEnv.toDate(this._instance.range.end) : null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "startStr", {
            get: function() {
                var e = this._instance;
                return e ? this._context.dateEnv.formatIso(e.range.start, {
                    omitTime: this._def.allDay,
                    forcedTzo: e.forcedStartTzo
                }) : ""
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "endStr", {
            get: function() {
                var e = this._instance;
                return e && this._def.hasEnd ? this._context.dateEnv.formatIso(e.range.end, {
                    omitTime: this._def.allDay,
                    forcedTzo: e.forcedEndTzo
                }) : ""
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "id", {
            get: function() {
                return this._def.publicId
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "groupId", {
            get: function() {
                return this._def.groupId
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "allDay", {
            get: function() {
                return this._def.allDay
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "title", {
            get: function() {
                return this._def.title
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "url", {
            get: function() {
                return this._def.url
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "display", {
            get: function() {
                return this._def.ui.display || "auto"
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "startEditable", {
            get: function() {
                return this._def.ui.startEditable
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "durationEditable", {
            get: function() {
                return this._def.ui.durationEditable
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "constraint", {
            get: function() {
                return this._def.ui.constraints[0] || null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "overlap", {
            get: function() {
                return this._def.ui.overlap
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "allow", {
            get: function() {
                return this._def.ui.allows[0] || null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "backgroundColor", {
            get: function() {
                return this._def.ui.backgroundColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "borderColor", {
            get: function() {
                return this._def.ui.borderColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "textColor", {
            get: function() {
                return this._def.ui.textColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "classNames", {
            get: function() {
                return this._def.ui.classNames
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "extendedProps", {
            get: function() {
                return this._def.extendedProps
            },
            enumerable: !1,
            configurable: !0
        }),
        e.prototype.toPlainObject = function(e) {
            void 0 === e && (e = {});
            var t = this._def
              , n = t.ui
              , o = this.startStr
              , i = this.endStr
              , a = {};
            return t.title && (a.title = t.title),
            o && (a.start = o),
            i && (a.end = i),
            t.publicId && (a.id = t.publicId),
            t.groupId && (a.groupId = t.groupId),
            t.url && (a.url = t.url),
            n.display && "auto" !== n.display && (a.display = n.display),
            e.collapseColor && n.backgroundColor && n.backgroundColor === n.borderColor ? a.color = n.backgroundColor : (n.backgroundColor && (a.backgroundColor = n.backgroundColor),
            n.borderColor && (a.borderColor = n.borderColor)),
            n.textColor && (a.textColor = n.textColor),
            n.classNames.length && (a.classNames = n.classNames),
            Object.keys(t.extendedProps).length && (e.collapseExtendedProps ? r(a, t.extendedProps) : a.extendedProps = t.extendedProps),
            a
        }
        ,
        e.prototype.toJSON = function() {
            return this.toPlainObject()
        }
        ,
        e
    }();
    function Xr(e) {
        var t, n, r = e._def, o = e._instance;
        return {
            defs: (t = {},
            t[r.defId] = r,
            t),
            instances: o ? (n = {},
            n[o.instanceId] = o,
            n) : {}
        }
    }
    function Kr(e, t, n) {
        var r = e.defs
          , o = e.instances
          , i = []
          , a = n ? n.instanceId : "";
        for (var s in o) {
            var l = o[s]
              , u = r[l.defId];
            l.instanceId !== a && i.push(new Zr(t,u,l))
        }
        return i
    }
    var $r = {};
    var Jr, Qr = function() {
        function e() {}
        return e.prototype.getMarkerYear = function(e) {
            return e.getUTCFullYear()
        }
        ,
        e.prototype.getMarkerMonth = function(e) {
            return e.getUTCMonth()
        }
        ,
        e.prototype.getMarkerDay = function(e) {
            return e.getUTCDate()
        }
        ,
        e.prototype.arrayToMarker = function(e) {
            return _t(e)
        }
        ,
        e.prototype.markerToArray = function(e) {
            return Tt(e)
        }
        ,
        e
    }();
    Jr = Qr,
    $r["gregory"] = Jr;
    var eo = /^\s*(\d{4})(-?(\d{2})(-?(\d{2})([T ](\d{2}):?(\d{2})(:?(\d{2})(\.(\d+))?)?(Z|(([-+])(\d{2})(:?(\d{2}))?))?)?)?)?$/;
    function to(e) {
        var t = eo.exec(e);
        if (t) {
            var n = new Date(Date.UTC(Number(t[1]), t[3] ? Number(t[3]) - 1 : 0, Number(t[5] || 1), Number(t[7] || 0), Number(t[8] || 0), Number(t[10] || 0), t[12] ? 1e3 * Number("0." + t[12]) : 0));
            if (xt(n)) {
                var r = null;
                return t[13] && (r = ("-" === t[15] ? -1 : 1) * (60 * Number(t[16] || 0) + Number(t[18] || 0))),
                {
                    marker: n,
                    isTimeUnspecified: !t[6],
                    timeZoneOffset: r
                }
            }
        }
        return null
    }
    var no = function() {
        function e(e) {
            var t = this.timeZone = e.timeZone
              , n = "local" !== t && "UTC" !== t;
            e.namedTimeZoneImpl && n && (this.namedTimeZoneImpl = new e.namedTimeZoneImpl(t)),
            this.canComputeOffset = Boolean(!n || this.namedTimeZoneImpl),
            this.calendarSystem = function(e) {
                return new $r[e]
            }(e.calendarSystem),
            this.locale = e.locale,
            this.weekDow = e.locale.week.dow,
            this.weekDoy = e.locale.week.doy,
            "ISO" === e.weekNumberCalculation && (this.weekDow = 1,
            this.weekDoy = 4),
            "number" == typeof e.firstDay && (this.weekDow = e.firstDay),
            "function" == typeof e.weekNumberCalculation && (this.weekNumberFunc = e.weekNumberCalculation),
            this.weekText = null != e.weekText ? e.weekText : e.locale.options.weekText,
            this.weekTextLong = (null != e.weekTextLong ? e.weekTextLong : e.locale.options.weekTextLong) || this.weekText,
            this.cmdFormatter = e.cmdFormatter,
            this.defaultSeparator = e.defaultSeparator
        }
        return e.prototype.createMarker = function(e) {
            var t = this.createMarkerMeta(e);
            return null === t ? null : t.marker
        }
        ,
        e.prototype.createNowMarker = function() {
            return this.canComputeOffset ? this.timestampToMarker((new Date).valueOf()) : _t(Rt(new Date))
        }
        ,
        e.prototype.createMarkerMeta = function(e) {
            if ("string" == typeof e)
                return this.parse(e);
            var t = null;
            return "number" == typeof e ? t = this.timestampToMarker(e) : e instanceof Date ? (e = e.valueOf(),
            isNaN(e) || (t = this.timestampToMarker(e))) : Array.isArray(e) && (t = _t(e)),
            null !== t && xt(t) ? {
                marker: t,
                isTimeUnspecified: !1,
                forcedTzo: null
            } : null
        }
        ,
        e.prototype.parse = function(e) {
            var t = to(e);
            if (null === t)
                return null;
            var n = t.marker
              , r = null;
            return null !== t.timeZoneOffset && (this.canComputeOffset ? n = this.timestampToMarker(n.valueOf() - 60 * t.timeZoneOffset * 1e3) : r = t.timeZoneOffset),
            {
                marker: n,
                isTimeUnspecified: t.isTimeUnspecified,
                forcedTzo: r
            }
        }
        ,
        e.prototype.getYear = function(e) {
            return this.calendarSystem.getMarkerYear(e)
        }
        ,
        e.prototype.getMonth = function(e) {
            return this.calendarSystem.getMarkerMonth(e)
        }
        ,
        e.prototype.add = function(e, t) {
            var n = this.calendarSystem.markerToArray(e);
            return n[0] += t.years,
            n[1] += t.months,
            n[2] += t.days,
            n[6] += t.milliseconds,
            this.calendarSystem.arrayToMarker(n)
        }
        ,
        e.prototype.subtract = function(e, t) {
            var n = this.calendarSystem.markerToArray(e);
            return n[0] -= t.years,
            n[1] -= t.months,
            n[2] -= t.days,
            n[6] -= t.milliseconds,
            this.calendarSystem.arrayToMarker(n)
        }
        ,
        e.prototype.addYears = function(e, t) {
            var n = this.calendarSystem.markerToArray(e);
            return n[0] += t,
            this.calendarSystem.arrayToMarker(n)
        }
        ,
        e.prototype.addMonths = function(e, t) {
            var n = this.calendarSystem.markerToArray(e);
            return n[1] += t,
            this.calendarSystem.arrayToMarker(n)
        }
        ,
        e.prototype.diffWholeYears = function(e, t) {
            var n = this.calendarSystem;
            return kt(e) === kt(t) && n.getMarkerDay(e) === n.getMarkerDay(t) && n.getMarkerMonth(e) === n.getMarkerMonth(t) ? n.getMarkerYear(t) - n.getMarkerYear(e) : null
        }
        ,
        e.prototype.diffWholeMonths = function(e, t) {
            var n = this.calendarSystem;
            return kt(e) === kt(t) && n.getMarkerDay(e) === n.getMarkerDay(t) ? n.getMarkerMonth(t) - n.getMarkerMonth(e) + 12 * (n.getMarkerYear(t) - n.getMarkerYear(e)) : null
        }
        ,
        e.prototype.greatestWholeUnit = function(e, t) {
            var n = this.diffWholeYears(e, t);
            return null !== n ? {
                unit: "year",
                value: n
            } : null !== (n = this.diffWholeMonths(e, t)) ? {
                unit: "month",
                value: n
            } : null !== (n = St(e, t)) ? {
                unit: "week",
                value: n
            } : null !== (n = Et(e, t)) ? {
                unit: "day",
                value: n
            } : ct(n = function(e, t) {
                return (t.valueOf() - e.valueOf()) / 36e5
            }(e, t)) ? {
                unit: "hour",
                value: n
            } : ct(n = function(e, t) {
                return (t.valueOf() - e.valueOf()) / 6e4
            }(e, t)) ? {
                unit: "minute",
                value: n
            } : ct(n = function(e, t) {
                return (t.valueOf() - e.valueOf()) / 1e3
            }(e, t)) ? {
                unit: "second",
                value: n
            } : {
                unit: "millisecond",
                value: t.valueOf() - e.valueOf()
            }
        }
        ,
        e.prototype.countDurationsBetween = function(e, t, n) {
            var r;
            return n.years && null !== (r = this.diffWholeYears(e, t)) ? r / ($t(n) / 365) : n.months && null !== (r = this.diffWholeMonths(e, t)) ? r / function(e) {
                return $t(e) / 30
            }(n) : n.days && null !== (r = Et(e, t)) ? r / $t(n) : (t.valueOf() - e.valueOf()) / en(n)
        }
        ,
        e.prototype.startOf = function(e, t) {
            return "year" === t ? this.startOfYear(e) : "month" === t ? this.startOfMonth(e) : "week" === t ? this.startOfWeek(e) : "day" === t ? bt(e) : "hour" === t ? function(e) {
                return _t([e.getUTCFullYear(), e.getUTCMonth(), e.getUTCDate(), e.getUTCHours()])
            }(e) : "minute" === t ? function(e) {
                return _t([e.getUTCFullYear(), e.getUTCMonth(), e.getUTCDate(), e.getUTCHours(), e.getUTCMinutes()])
            }(e) : "second" === t ? function(e) {
                return _t([e.getUTCFullYear(), e.getUTCMonth(), e.getUTCDate(), e.getUTCHours(), e.getUTCMinutes(), e.getUTCSeconds()])
            }(e) : null
        }
        ,
        e.prototype.startOfYear = function(e) {
            return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e)])
        }
        ,
        e.prototype.startOfMonth = function(e) {
            return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e), this.calendarSystem.getMarkerMonth(e)])
        }
        ,
        e.prototype.startOfWeek = function(e) {
            return this.calendarSystem.arrayToMarker([this.calendarSystem.getMarkerYear(e), this.calendarSystem.getMarkerMonth(e), e.getUTCDate() - (e.getUTCDay() - this.weekDow + 7) % 7])
        }
        ,
        e.prototype.computeWeekNumber = function(e) {
            return this.weekNumberFunc ? this.weekNumberFunc(this.toDate(e)) : function(e, t, n) {
                var r = e.getUTCFullYear()
                  , o = Ct(e, r, t, n);
                if (o < 1)
                    return Ct(e, r - 1, t, n);
                var i = Ct(e, r + 1, t, n);
                return i >= 1 ? Math.min(o, i) : o
            }(e, this.weekDow, this.weekDoy)
        }
        ,
        e.prototype.format = function(e, t, n) {
            return void 0 === n && (n = {}),
            t.format({
                marker: e,
                timeZoneOffset: null != n.forcedTzo ? n.forcedTzo : this.offsetForMarker(e)
            }, this)
        }
        ,
        e.prototype.formatRange = function(e, t, n, r) {
            return void 0 === r && (r = {}),
            r.isEndExclusive && (t = vt(t, -1)),
            n.formatRange({
                marker: e,
                timeZoneOffset: null != r.forcedStartTzo ? r.forcedStartTzo : this.offsetForMarker(e)
            }, {
                marker: t,
                timeZoneOffset: null != r.forcedEndTzo ? r.forcedEndTzo : this.offsetForMarker(t)
            }, this, r.defaultSeparator)
        }
        ,
        e.prototype.formatIso = function(e, t) {
            void 0 === t && (t = {});
            var n = null;
            return t.omitTimeZoneOffset || (n = null != t.forcedTzo ? t.forcedTzo : this.offsetForMarker(e)),
            rn(e, n, t.omitTime)
        }
        ,
        e.prototype.timestampToMarker = function(e) {
            return "local" === this.timeZone ? _t(Rt(new Date(e))) : "UTC" !== this.timeZone && this.namedTimeZoneImpl ? _t(this.namedTimeZoneImpl.timestampToArray(e)) : new Date(e)
        }
        ,
        e.prototype.offsetForMarker = function(e) {
            return "local" === this.timeZone ? -wt(Tt(e)).getTimezoneOffset() : "UTC" === this.timeZone ? 0 : this.namedTimeZoneImpl ? this.namedTimeZoneImpl.offsetForArray(Tt(e)) : null
        }
        ,
        e.prototype.toDate = function(e, t) {
            return "local" === this.timeZone ? wt(Tt(e)) : "UTC" === this.timeZone ? new Date(e.valueOf()) : this.namedTimeZoneImpl ? new Date(e.valueOf() - 1e3 * this.namedTimeZoneImpl.offsetForArray(Tt(e)) * 60) : new Date(e.valueOf() - (t || 0))
        }
        ,
        e
    }()
      , ro = []
      , oo = {
        code: "en",
        week: {
            dow: 0,
            doy: 4
        },
        direction: "ltr",
        buttonText: {
            prev: "prev",
            next: "next",
            prevYear: "prev year",
            nextYear: "next year",
            year: "year",
            today: "today",
            month: "month",
            week: "week",
            day: "day",
            list: "list"
        },
        weekText: "W",
        weekTextLong: "Week",
        closeHint: "Close",
        timeHint: "Time",
        eventHint: "Event",
        allDayText: "all-day",
        moreLinkText: "more",
        noEventsText: "No events to display"
    }
      , io = r(r({}, oo), {
        buttonHints: {
            prev: "Previous $0",
            next: "Next $0",
            today: function(e, t) {
                return "day" === t ? "Today" : "This " + e
            }
        },
        viewHint: "$0 view",
        navLinkHint: "Go to $0",
        moreLinkHint: function(e) {
            return "Show " + e + " more event" + (1 === e ? "" : "s")
        }
    });
    function ao(e) {
        for (var t = e.length > 0 ? e[0].code : "en", n = ro.concat(e), r = {
            en: io
        }, o = 0, i = n; o < i.length; o++) {
            var a = i[o];
            r[a.code] = a
        }
        return {
            map: r,
            defaultCode: t
        }
    }
    function so(e, t) {
        return "object" != typeof e || Array.isArray(e) ? function(e, t) {
            var n = [].concat(e || [])
              , r = function(e, t) {
                for (var n = 0; n < e.length; n += 1)
                    for (var r = e[n].toLocaleLowerCase().split("-"), o = r.length; o > 0; o -= 1) {
                        var i = r.slice(0, o).join("-");
                        if (t[i])
                            return t[i]
                    }
                return null
            }(n, t) || io;
            return lo(e, n, r)
        }(e, t) : lo(e.code, [e.code], e)
    }
    function lo(e, t, n) {
        var r = Pt([oo, n], ["buttonText"]);
        delete r.code;
        var o = r.week;
        return delete r.week,
        {
            codeArg: e,
            codes: t,
            week: o,
            simpleNumberFormat: new Intl.NumberFormat(e),
            options: r
        }
    }
    function uo(e) {
        var t = so(e.locale || "en", ao([]).map);
        return new no(r(r({
            timeZone: kn.timeZone,
            calendarSystem: "gregory"
        }, e), {
            locale: t
        }))
    }
    var co, po = {
        startTime: "09:00",
        endTime: "17:00",
        daysOfWeek: [1, 2, 3, 4, 5],
        display: "inverse-background",
        classNames: "fc-non-business",
        groupId: "_businessHours"
    };
    function fo(e, t) {
        return Ln(function(e) {
            var t;
            t = !0 === e ? [{}] : Array.isArray(e) ? e.filter((function(e) {
                return e.daysOfWeek
            }
            )) : "object" == typeof e && e ? [e] : [];
            return t = t.map((function(e) {
                return r(r({}, po), e)
            }
            ))
        }(e), null, t)
    }
    function ho(e, t) {
        return e.left >= t.left && e.left < t.right && e.top >= t.top && e.top < t.bottom
    }
    function vo(e, t) {
        var n = {
            left: Math.max(e.left, t.left),
            right: Math.min(e.right, t.right),
            top: Math.max(e.top, t.top),
            bottom: Math.min(e.bottom, t.bottom)
        };
        return n.left < n.right && n.top < n.bottom && n
    }
    function go(e, t, n) {
        return {
            left: e.left + t,
            right: e.right + t,
            top: e.top + n,
            bottom: e.bottom + n
        }
    }
    function mo(e, t) {
        return {
            left: Math.min(Math.max(e.left, t.left), t.right),
            top: Math.min(Math.max(e.top, t.top), t.bottom)
        }
    }
    function yo(e) {
        return {
            left: (e.left + e.right) / 2,
            top: (e.top + e.bottom) / 2
        }
    }
    function So(e, t) {
        return {
            left: e.left - t.left,
            top: e.top - t.top
        }
    }
    function Eo() {
        return null == co && (co = function() {
            if ("undefined" == typeof document)
                return !0;
            var e = document.createElement("div");
            e.style.position = "absolute",
            e.style.top = "0px",
            e.style.left = "0px",
            e.innerHTML = "<table><tr><td><div></div></td></tr></table>",
            e.querySelector("table").style.height = "100px",
            e.querySelector("div").style.height = "100%",
            document.body.appendChild(e);
            var t = e.querySelector("div").offsetHeight > 0;
            return document.body.removeChild(e),
            t
        }()),
        co
    }
    var bo = {
        defs: {},
        instances: {}
    }
      , Co = function() {
        function e() {
            this.getKeysForEventDefs = cn(this._getKeysForEventDefs),
            this.splitDateSelection = cn(this._splitDateSpan),
            this.splitEventStore = cn(this._splitEventStore),
            this.splitIndividualUi = cn(this._splitIndividualUi),
            this.splitEventDrag = cn(this._splitInteraction),
            this.splitEventResize = cn(this._splitInteraction),
            this.eventUiBuilders = {}
        }
        return e.prototype.splitProps = function(e) {
            var t = this
              , n = this.getKeyInfo(e)
              , r = this.getKeysForEventDefs(e.eventStore)
              , o = this.splitDateSelection(e.dateSelection)
              , i = this.splitIndividualUi(e.eventUiBases, r)
              , a = this.splitEventStore(e.eventStore, r)
              , s = this.splitEventDrag(e.eventDrag)
              , l = this.splitEventResize(e.eventResize)
              , u = {};
            for (var c in this.eventUiBuilders = Ht(n, (function(e, n) {
                return t.eventUiBuilders[n] || cn(Do)
            }
            )),
            n) {
                var d = n[c]
                  , p = a[c] || bo
                  , f = this.eventUiBuilders[c];
                u[c] = {
                    businessHours: d.businessHours || e.businessHours,
                    dateSelection: o[c] || null,
                    eventStore: p,
                    eventUiBases: f(e.eventUiBases[""], d.ui, i[c]),
                    eventSelection: p.instances[e.eventSelection] ? e.eventSelection : "",
                    eventDrag: s[c] || null,
                    eventResize: l[c] || null
                }
            }
            return u
        }
        ,
        e.prototype._splitDateSpan = function(e) {
            var t = {};
            if (e)
                for (var n = 0, r = this.getKeysForDateSpan(e); n < r.length; n++) {
                    t[r[n]] = e
                }
            return t
        }
        ,
        e.prototype._getKeysForEventDefs = function(e) {
            var t = this;
            return Ht(e.defs, (function(e) {
                return t.getKeysForEventDef(e)
            }
            ))
        }
        ,
        e.prototype._splitEventStore = function(e, t) {
            var n = e.defs
              , r = e.instances
              , o = {};
            for (var i in n)
                for (var a = 0, s = t[i]; a < s.length; a++) {
                    o[p = s[a]] || (o[p] = {
                        defs: {},
                        instances: {}
                    }),
                    o[p].defs[i] = n[i]
                }
            for (var l in r)
                for (var u = r[l], c = 0, d = t[u.defId]; c < d.length; c++) {
                    var p;
                    o[p = d[c]] && (o[p].instances[l] = u)
                }
            return o
        }
        ,
        e.prototype._splitIndividualUi = function(e, t) {
            var n = {};
            for (var r in e)
                if (r)
                    for (var o = 0, i = t[r]; o < i.length; o++) {
                        var a = i[o];
                        n[a] || (n[a] = {}),
                        n[a][r] = e[r]
                    }
            return n
        }
        ,
        e.prototype._splitInteraction = function(e) {
            var t = {};
            if (e) {
                var n = this._splitEventStore(e.affectedEvents, this._getKeysForEventDefs(e.affectedEvents))
                  , r = this._getKeysForEventDefs(e.mutatedEvents)
                  , o = this._splitEventStore(e.mutatedEvents, r)
                  , i = function(r) {
                    t[r] || (t[r] = {
                        affectedEvents: n[r] || bo,
                        mutatedEvents: o[r] || bo,
                        isEvent: e.isEvent
                    })
                };
                for (var a in n)
                    i(a);
                for (var a in o)
                    i(a)
            }
            return t
        }
        ,
        e
    }();
    function Do(e, t, n) {
        var o = [];
        e && o.push(e),
        t && o.push(t);
        var i = {
            "": Zn(o)
        };
        return n && r(i, n),
        i
    }
    function Ro(e, t, n, r) {
        return {
            dow: e.getUTCDay(),
            isDisabled: Boolean(r && !fr(r.activeRange, e)),
            isOther: Boolean(r && !fr(r.currentRange, e)),
            isToday: Boolean(t && fr(t, e)),
            isPast: Boolean(n ? e < n : !!t && e < t.start),
            isFuture: Boolean(n ? e > n : !!t && e >= t.end)
        }
    }
    function wo(e, t) {
        var n = ["fc-day", "fc-day-" + pt[e.dow]];
        return e.isDisabled ? n.push("fc-day-disabled") : (e.isToday && (n.push("fc-day-today"),
        n.push(t.getClass("today"))),
        e.isPast && n.push("fc-day-past"),
        e.isFuture && n.push("fc-day-future"),
        e.isOther && n.push("fc-day-other")),
        n
    }
    function To(e, t) {
        var n = ["fc-slot", "fc-slot-" + pt[e.dow]];
        return e.isDisabled ? n.push("fc-slot-disabled") : (e.isToday && (n.push("fc-slot-today"),
        n.push(t.getClass("today"))),
        e.isPast && n.push("fc-slot-past"),
        e.isFuture && n.push("fc-slot-future")),
        n
    }
    var _o = _n({
        year: "numeric",
        month: "long",
        day: "numeric"
    })
      , xo = _n({
        week: "long"
    });
    function ko(e, t, n, o) {
        void 0 === n && (n = "day"),
        void 0 === o && (o = !0);
        var i = e.dateEnv
          , a = e.options
          , s = e.calendarApi
          , l = i.format(t, "week" === n ? xo : _o);
        if (a.navLinks) {
            var u = i.toDate(t)
              , c = function(e) {
                var r = "day" === n ? a.navLinkDayClick : "week" === n ? a.navLinkWeekClick : null;
                "function" == typeof r ? r.call(s, i.toDate(t), e) : ("string" == typeof r && (n = r),
                s.zoomTo(t, n))
            };
            return r({
                title: lt(a.navLinkHint, [l, u], l),
                "data-navlink": ""
            }, o ? Ye(c) : {
                onClick: c
            })
        }
        return {
            "aria-label": l
        }
    }
    var Mo, Io = null;
    function Po() {
        return null === Io && (Io = function() {
            var e = document.createElement("div");
            We(e, {
                position: "absolute",
                top: -1e3,
                left: 0,
                border: 0,
                padding: 0,
                overflow: "scroll",
                direction: "rtl"
            }),
            e.innerHTML = "<div></div>",
            document.body.appendChild(e);
            var t = e.firstChild.getBoundingClientRect().left > e.getBoundingClientRect().left;
            return Ie(e),
            t
        }()),
        Io
    }
    function No() {
        return Mo || (Mo = function() {
            var e = document.createElement("div");
            e.style.overflow = "scroll",
            e.style.position = "absolute",
            e.style.top = "-9999px",
            e.style.left = "-9999px",
            document.body.appendChild(e);
            var t = Ho(e);
            return document.body.removeChild(e),
            t
        }()),
        Mo
    }
    function Ho(e) {
        return {
            x: e.offsetHeight - e.clientHeight,
            y: e.offsetWidth - e.clientWidth
        }
    }
    function Oo(e, t) {
        void 0 === t && (t = !1);
        var n = window.getComputedStyle(e)
          , r = parseInt(n.borderLeftWidth, 10) || 0
          , o = parseInt(n.borderRightWidth, 10) || 0
          , i = parseInt(n.borderTopWidth, 10) || 0
          , a = parseInt(n.borderBottomWidth, 10) || 0
          , s = Ho(e)
          , l = s.y - r - o
          , u = {
            borderLeft: r,
            borderRight: o,
            borderTop: i,
            borderBottom: a,
            scrollbarBottom: s.x - i - a,
            scrollbarLeft: 0,
            scrollbarRight: 0
        };
        return Po() && "rtl" === n.direction ? u.scrollbarLeft = l : u.scrollbarRight = l,
        t && (u.paddingLeft = parseInt(n.paddingLeft, 10) || 0,
        u.paddingRight = parseInt(n.paddingRight, 10) || 0,
        u.paddingTop = parseInt(n.paddingTop, 10) || 0,
        u.paddingBottom = parseInt(n.paddingBottom, 10) || 0),
        u
    }
    function Ao(e, t, n) {
        void 0 === t && (t = !1);
        var r = n ? e.getBoundingClientRect() : Wo(e)
          , o = Oo(e, t)
          , i = {
            left: r.left + o.borderLeft + o.scrollbarLeft,
            right: r.right - o.borderRight - o.scrollbarRight,
            top: r.top + o.borderTop,
            bottom: r.bottom - o.borderBottom - o.scrollbarBottom
        };
        return t && (i.left += o.paddingLeft,
        i.right -= o.paddingRight,
        i.top += o.paddingTop,
        i.bottom -= o.paddingBottom),
        i
    }
    function Wo(e) {
        var t = e.getBoundingClientRect();
        return {
            left: t.left + window.pageXOffset,
            top: t.top + window.pageYOffset,
            right: t.right + window.pageXOffset,
            bottom: t.bottom + window.pageYOffset
        }
    }
    function Lo(e) {
        for (var t = []; e instanceof HTMLElement; ) {
            var n = window.getComputedStyle(e);
            if ("fixed" === n.position)
                break;
            /(auto|scroll)/.test(n.overflow + n.overflowY + n.overflowX) && t.push(e),
            e = e.parentNode
        }
        return t
    }
    function Uo(e, t, n) {
        var r = !1
          , o = function() {
            r || (r = !0,
            t.apply(this, arguments))
        }
          , i = function() {
            r || (r = !0,
            n && n.apply(this, arguments))
        }
          , a = e(o, i);
        a && "function" == typeof a.then && a.then(o, i)
    }
    var Bo = function() {
        function e() {
            this.handlers = {},
            this.thisContext = null
        }
        return e.prototype.setThisContext = function(e) {
            this.thisContext = e
        }
        ,
        e.prototype.setOptions = function(e) {
            this.options = e
        }
        ,
        e.prototype.on = function(e, t) {
            !function(e, t, n) {
                (e[t] || (e[t] = [])).push(n)
            }(this.handlers, e, t)
        }
        ,
        e.prototype.off = function(e, t) {
            !function(e, t, n) {
                n ? e[t] && (e[t] = e[t].filter((function(e) {
                    return e !== n
                }
                ))) : delete e[t]
            }(this.handlers, e, t)
        }
        ,
        e.prototype.trigger = function(e) {
            for (var t = [], n = 1; n < arguments.length; n++)
                t[n - 1] = arguments[n];
            for (var r = this.handlers[e] || [], o = this.options && this.options[e], i = [].concat(o || [], r), a = 0, s = i; a < s.length; a++) {
                var l = s[a];
                l.apply(this.thisContext, t)
            }
        }
        ,
        e.prototype.hasHandlers = function(e) {
            return Boolean(this.handlers[e] && this.handlers[e].length || this.options && this.options[e])
        }
        ,
        e
    }();
    var zo = function() {
        function e(e, t, n, r) {
            this.els = t;
            var o = this.originClientRect = e.getBoundingClientRect();
            n && this.buildElHorizontals(o.left),
            r && this.buildElVerticals(o.top)
        }
        return e.prototype.buildElHorizontals = function(e) {
            for (var t = [], n = [], r = 0, o = this.els; r < o.length; r++) {
                var i = o[r].getBoundingClientRect();
                t.push(i.left - e),
                n.push(i.right - e)
            }
            this.lefts = t,
            this.rights = n
        }
        ,
        e.prototype.buildElVerticals = function(e) {
            for (var t = [], n = [], r = 0, o = this.els; r < o.length; r++) {
                var i = o[r].getBoundingClientRect();
                t.push(i.top - e),
                n.push(i.bottom - e)
            }
            this.tops = t,
            this.bottoms = n
        }
        ,
        e.prototype.leftToIndex = function(e) {
            var t, n = this.lefts, r = this.rights, o = n.length;
            for (t = 0; t < o; t += 1)
                if (e >= n[t] && e < r[t])
                    return t
        }
        ,
        e.prototype.topToIndex = function(e) {
            var t, n = this.tops, r = this.bottoms, o = n.length;
            for (t = 0; t < o; t += 1)
                if (e >= n[t] && e < r[t])
                    return t
        }
        ,
        e.prototype.getWidth = function(e) {
            return this.rights[e] - this.lefts[e]
        }
        ,
        e.prototype.getHeight = function(e) {
            return this.bottoms[e] - this.tops[e]
        }
        ,
        e
    }()
      , Vo = function() {
        function e() {}
        return e.prototype.getMaxScrollTop = function() {
            return this.getScrollHeight() - this.getClientHeight()
        }
        ,
        e.prototype.getMaxScrollLeft = function() {
            return this.getScrollWidth() - this.getClientWidth()
        }
        ,
        e.prototype.canScrollVertically = function() {
            return this.getMaxScrollTop() > 0
        }
        ,
        e.prototype.canScrollHorizontally = function() {
            return this.getMaxScrollLeft() > 0
        }
        ,
        e.prototype.canScrollUp = function() {
            return this.getScrollTop() > 0
        }
        ,
        e.prototype.canScrollDown = function() {
            return this.getScrollTop() < this.getMaxScrollTop()
        }
        ,
        e.prototype.canScrollLeft = function() {
            return this.getScrollLeft() > 0
        }
        ,
        e.prototype.canScrollRight = function() {
            return this.getScrollLeft() < this.getMaxScrollLeft()
        }
        ,
        e
    }()
      , Fo = function(e) {
        function t(t) {
            var n = e.call(this) || this;
            return n.el = t,
            n
        }
        return n(t, e),
        t.prototype.getScrollTop = function() {
            return this.el.scrollTop
        }
        ,
        t.prototype.getScrollLeft = function() {
            return this.el.scrollLeft
        }
        ,
        t.prototype.setScrollTop = function(e) {
            this.el.scrollTop = e
        }
        ,
        t.prototype.setScrollLeft = function(e) {
            this.el.scrollLeft = e
        }
        ,
        t.prototype.getScrollWidth = function() {
            return this.el.scrollWidth
        }
        ,
        t.prototype.getScrollHeight = function() {
            return this.el.scrollHeight
        }
        ,
        t.prototype.getClientHeight = function() {
            return this.el.clientHeight
        }
        ,
        t.prototype.getClientWidth = function() {
            return this.el.clientWidth
        }
        ,
        t
    }(Vo)
      , Go = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.getScrollTop = function() {
            return window.pageYOffset
        }
        ,
        t.prototype.getScrollLeft = function() {
            return window.pageXOffset
        }
        ,
        t.prototype.setScrollTop = function(e) {
            window.scroll(window.pageXOffset, e)
        }
        ,
        t.prototype.setScrollLeft = function(e) {
            window.scroll(e, window.pageYOffset)
        }
        ,
        t.prototype.getScrollWidth = function() {
            return document.documentElement.scrollWidth
        }
        ,
        t.prototype.getScrollHeight = function() {
            return document.documentElement.scrollHeight
        }
        ,
        t.prototype.getClientHeight = function() {
            return document.documentElement.clientHeight
        }
        ,
        t.prototype.getClientWidth = function() {
            return document.documentElement.clientWidth
        }
        ,
        t
    }(Vo)
      , jo = function() {
        function e(e) {
            this.iconOverrideOption && this.setIconOverride(e[this.iconOverrideOption])
        }
        return e.prototype.setIconOverride = function(e) {
            var t, n;
            if ("object" == typeof e && e) {
                for (n in t = r({}, this.iconClasses),
                e)
                    t[n] = this.applyIconOverridePrefix(e[n]);
                this.iconClasses = t
            } else
                !1 === e && (this.iconClasses = {})
        }
        ,
        e.prototype.applyIconOverridePrefix = function(e) {
            var t = this.iconOverridePrefix;
            return t && 0 !== e.indexOf(t) && (e = t + e),
            e
        }
        ,
        e.prototype.getClass = function(e) {
            return this.classes[e] || ""
        }
        ,
        e.prototype.getIconClass = function(e, t) {
            var n;
            return (n = t && this.rtlIconClasses && this.rtlIconClasses[e] || this.iconClasses[e]) ? this.baseIconClass + " " + n : ""
        }
        ,
        e.prototype.getCustomButtonIconClass = function(e) {
            var t;
            return this.iconOverrideCustomButtonOption && (t = e[this.iconOverrideCustomButtonOption]) ? this.baseIconClass + " " + this.applyIconOverridePrefix(t) : ""
        }
        ,
        e
    }();
    if (jo.prototype.classes = {},
    jo.prototype.iconClasses = {},
    jo.prototype.baseIconClass = "",
    jo.prototype.iconOverridePrefix = "",
    "undefined" == typeof FullCalendarVDom)
        throw new Error("Please import the top-level fullcalendar lib before attempting to import a plugin.");
    var qo = FullCalendarVDom.Component
      , Yo = FullCalendarVDom.createElement
      , Zo = FullCalendarVDom.render
      , Xo = FullCalendarVDom.createRef
      , Ko = FullCalendarVDom.Fragment
      , $o = FullCalendarVDom.createContext
      , Jo = FullCalendarVDom.createPortal
      , Qo = FullCalendarVDom.flushSync
      , ei = FullCalendarVDom.unmountComponentAtNode
      , ti = function() {
        function e(e, t, n, o) {
            var i = this;
            this.execFunc = e,
            this.emitter = t,
            this.scrollTime = n,
            this.scrollTimeReset = o,
            this.handleScrollRequest = function(e) {
                i.queuedRequest = r({}, i.queuedRequest || {}, e),
                i.drain()
            }
            ,
            t.on("_scrollRequest", this.handleScrollRequest),
            this.fireInitialScroll()
        }
        return e.prototype.detach = function() {
            this.emitter.off("_scrollRequest", this.handleScrollRequest)
        }
        ,
        e.prototype.update = function(e) {
            e && this.scrollTimeReset ? this.fireInitialScroll() : this.drain()
        }
        ,
        e.prototype.fireInitialScroll = function() {
            this.handleScrollRequest({
                time: this.scrollTime
            })
        }
        ,
        e.prototype.drain = function() {
            this.queuedRequest && this.execFunc(this.queuedRequest) && (this.queuedRequest = null)
        }
        ,
        e
    }()
      , ni = $o({});
    function ri(e, t, n, r, o, i, a, s, l, u, c, d, p) {
        return {
            dateEnv: o,
            options: n,
            pluginHooks: a,
            emitter: u,
            dispatch: s,
            getCurrentData: l,
            calendarApi: c,
            viewSpec: e,
            viewApi: t,
            dateProfileGenerator: r,
            theme: i,
            isRtl: "rtl" === n.direction,
            addResizeHandler: function(e) {
                u.on("_resize", e)
            },
            removeResizeHandler: function(e) {
                u.off("_resize", e)
            },
            createScrollResponder: function(e) {
                return new ti(e,u,qt(n.scrollTime),n.scrollTimeReset)
            },
            registerInteractiveComponent: d,
            unregisterInteractiveComponent: p
        }
    }
    var oi = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.shouldComponentUpdate = function(e, t) {
            return this.debug && console.log(Lt(e, this.props), Lt(t, this.state)),
            !Ut(this.props, e, this.propEquality) || !Ut(this.state, t, this.stateEquality)
        }
        ,
        t.prototype.safeSetState = function(e) {
            Ut(this.state, r(r({}, this.state), e), this.stateEquality) || this.setState(e)
        }
        ,
        t.addPropsEquality = ai,
        t.addStateEquality = si,
        t.contextType = ni,
        t
    }(qo);
    oi.prototype.propEquality = {},
    oi.prototype.stateEquality = {};
    var ii = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.contextType = ni,
        t
    }(oi);
    function ai(e) {
        var t = Object.create(this.prototype.propEquality);
        r(t, e),
        this.prototype.propEquality = t
    }
    function si(e) {
        var t = Object.create(this.prototype.stateEquality);
        r(t, e),
        this.prototype.stateEquality = t
    }
    function li(e, t) {
        "function" == typeof e ? e(t) : e && (e.current = t)
    }
    var ui = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.uid = Ke(),
            t
        }
        return n(t, e),
        t.prototype.prepareHits = function() {}
        ,
        t.prototype.queryHit = function(e, t, n, r) {
            return null
        }
        ,
        t.prototype.isValidSegDownEl = function(e) {
            return !this.props.eventDrag && !this.props.eventResize && !Pe(e, ".fc-event-mirror")
        }
        ,
        t.prototype.isValidDateDownEl = function(e) {
            return !(Pe(e, ".fc-event:not(.fc-bg-event)") || Pe(e, ".fc-more-link") || Pe(e, "a[data-navlink]") || Pe(e, ".fc-popover"))
        }
        ,
        t
    }(ii);
    function ci(e) {
        return {
            id: Ke(),
            deps: e.deps || [],
            reducers: e.reducers || [],
            isLoadingFuncs: e.isLoadingFuncs || [],
            contextInit: [].concat(e.contextInit || []),
            eventRefiners: e.eventRefiners || {},
            eventDefMemberAdders: e.eventDefMemberAdders || [],
            eventSourceRefiners: e.eventSourceRefiners || {},
            isDraggableTransformers: e.isDraggableTransformers || [],
            eventDragMutationMassagers: e.eventDragMutationMassagers || [],
            eventDefMutationAppliers: e.eventDefMutationAppliers || [],
            dateSelectionTransformers: e.dateSelectionTransformers || [],
            datePointTransforms: e.datePointTransforms || [],
            dateSpanTransforms: e.dateSpanTransforms || [],
            views: e.views || {},
            viewPropsTransformers: e.viewPropsTransformers || [],
            isPropsValid: e.isPropsValid || null,
            externalDefTransforms: e.externalDefTransforms || [],
            viewContainerAppends: e.viewContainerAppends || [],
            eventDropTransformers: e.eventDropTransformers || [],
            componentInteractions: e.componentInteractions || [],
            calendarInteractions: e.calendarInteractions || [],
            themeClasses: e.themeClasses || {},
            eventSourceDefs: e.eventSourceDefs || [],
            cmdFormatter: e.cmdFormatter,
            recurringTypes: e.recurringTypes || [],
            namedTimeZonedImpl: e.namedTimeZonedImpl,
            initialView: e.initialView || "",
            elementDraggingImpl: e.elementDraggingImpl,
            optionChangeHandlers: e.optionChangeHandlers || {},
            scrollGridImpl: e.scrollGridImpl || null,
            contentTypeHandlers: e.contentTypeHandlers || {},
            listenerRefiners: e.listenerRefiners || {},
            optionRefiners: e.optionRefiners || {},
            propSetHandlers: e.propSetHandlers || {}
        }
    }
    function di() {
        var e, t = [], n = [];
        return function(o, i) {
            return e && un(o, t) && un(i, n) || (e = function(e, t) {
                var n = {}
                  , o = {
                    reducers: [],
                    isLoadingFuncs: [],
                    contextInit: [],
                    eventRefiners: {},
                    eventDefMemberAdders: [],
                    eventSourceRefiners: {},
                    isDraggableTransformers: [],
                    eventDragMutationMassagers: [],
                    eventDefMutationAppliers: [],
                    dateSelectionTransformers: [],
                    datePointTransforms: [],
                    dateSpanTransforms: [],
                    views: {},
                    viewPropsTransformers: [],
                    isPropsValid: null,
                    externalDefTransforms: [],
                    viewContainerAppends: [],
                    eventDropTransformers: [],
                    componentInteractions: [],
                    calendarInteractions: [],
                    themeClasses: {},
                    eventSourceDefs: [],
                    cmdFormatter: null,
                    recurringTypes: [],
                    namedTimeZonedImpl: null,
                    initialView: "",
                    elementDraggingImpl: null,
                    optionChangeHandlers: {},
                    scrollGridImpl: null,
                    contentTypeHandlers: {},
                    listenerRefiners: {},
                    optionRefiners: {},
                    propSetHandlers: {}
                };
                function i(e) {
                    for (var t = 0, a = e; t < a.length; t++) {
                        var s = a[t];
                        n[s.id] || (n[s.id] = !0,
                        i(s.deps),
                        u = s,
                        o = {
                            reducers: (l = o).reducers.concat(u.reducers),
                            isLoadingFuncs: l.isLoadingFuncs.concat(u.isLoadingFuncs),
                            contextInit: l.contextInit.concat(u.contextInit),
                            eventRefiners: r(r({}, l.eventRefiners), u.eventRefiners),
                            eventDefMemberAdders: l.eventDefMemberAdders.concat(u.eventDefMemberAdders),
                            eventSourceRefiners: r(r({}, l.eventSourceRefiners), u.eventSourceRefiners),
                            isDraggableTransformers: l.isDraggableTransformers.concat(u.isDraggableTransformers),
                            eventDragMutationMassagers: l.eventDragMutationMassagers.concat(u.eventDragMutationMassagers),
                            eventDefMutationAppliers: l.eventDefMutationAppliers.concat(u.eventDefMutationAppliers),
                            dateSelectionTransformers: l.dateSelectionTransformers.concat(u.dateSelectionTransformers),
                            datePointTransforms: l.datePointTransforms.concat(u.datePointTransforms),
                            dateSpanTransforms: l.dateSpanTransforms.concat(u.dateSpanTransforms),
                            views: r(r({}, l.views), u.views),
                            viewPropsTransformers: l.viewPropsTransformers.concat(u.viewPropsTransformers),
                            isPropsValid: u.isPropsValid || l.isPropsValid,
                            externalDefTransforms: l.externalDefTransforms.concat(u.externalDefTransforms),
                            viewContainerAppends: l.viewContainerAppends.concat(u.viewContainerAppends),
                            eventDropTransformers: l.eventDropTransformers.concat(u.eventDropTransformers),
                            calendarInteractions: l.calendarInteractions.concat(u.calendarInteractions),
                            componentInteractions: l.componentInteractions.concat(u.componentInteractions),
                            themeClasses: r(r({}, l.themeClasses), u.themeClasses),
                            eventSourceDefs: l.eventSourceDefs.concat(u.eventSourceDefs),
                            cmdFormatter: u.cmdFormatter || l.cmdFormatter,
                            recurringTypes: l.recurringTypes.concat(u.recurringTypes),
                            namedTimeZonedImpl: u.namedTimeZonedImpl || l.namedTimeZonedImpl,
                            initialView: l.initialView || u.initialView,
                            elementDraggingImpl: l.elementDraggingImpl || u.elementDraggingImpl,
                            optionChangeHandlers: r(r({}, l.optionChangeHandlers), u.optionChangeHandlers),
                            scrollGridImpl: u.scrollGridImpl || l.scrollGridImpl,
                            contentTypeHandlers: r(r({}, l.contentTypeHandlers), u.contentTypeHandlers),
                            listenerRefiners: r(r({}, l.listenerRefiners), u.listenerRefiners),
                            optionRefiners: r(r({}, l.optionRefiners), u.optionRefiners),
                            propSetHandlers: r(r({}, l.propSetHandlers), u.propSetHandlers)
                        })
                    }
                    var l, u
                }
                return e && i(e),
                i(t),
                o
            }(o, i)),
            t = o,
            n = i,
            e
        }
    }
    var pi = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t
    }(jo);
    function fi(e, t, n, o) {
        if (t[e])
            return t[e];
        var i = function(e, t, n, o) {
            var i = n[e]
              , a = o[e]
              , s = function(e) {
                return i && null !== i[e] ? i[e] : a && null !== a[e] ? a[e] : null
            }
              , l = s("component")
              , u = s("superType")
              , c = null;
            if (u) {
                if (u === e)
                    throw new Error("Can't have a custom view type that references itself");
                c = fi(u, t, n, o)
            }
            !l && c && (l = c.component);
            if (!l)
                return null;
            return {
                type: e,
                component: l,
                defaults: r(r({}, c ? c.defaults : {}), i ? i.rawOptions : {}),
                overrides: r(r({}, c ? c.overrides : {}), a ? a.rawOptions : {})
            }
        }(e, t, n, o);
        return i && (t[e] = i),
        i
    }
    pi.prototype.classes = {
        root: "fc-theme-standard",
        tableCellShaded: "fc-cell-shaded",
        buttonGroup: "fc-button-group",
        button: "fc-button fc-button-primary",
        buttonActive: "fc-button-active"
    },
    pi.prototype.baseIconClass = "fc-icon",
    pi.prototype.iconClasses = {
        close: "fc-icon-x",
        prev: "fc-icon-chevron-left",
        next: "fc-icon-chevron-right",
        prevYear: "fc-icon-chevrons-left",
        nextYear: "fc-icon-chevrons-right"
    },
    pi.prototype.rtlIconClasses = {
        prev: "fc-icon-chevron-right",
        next: "fc-icon-chevron-left",
        prevYear: "fc-icon-chevrons-right",
        nextYear: "fc-icon-chevrons-left"
    },
    pi.prototype.iconOverrideOption = "buttonIcons",
    pi.prototype.iconOverrideCustomButtonOption = "icon",
    pi.prototype.iconOverridePrefix = "fc-icon-";
    var hi = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t.handleRootEl = function(e) {
                li(t.rootElRef, e),
                t.props.elRef && li(t.props.elRef, e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = t.hookProps;
            return Yo(yi, {
                hookProps: n,
                didMount: t.didMount,
                willUnmount: t.willUnmount,
                elRef: this.handleRootEl
            }, (function(r) {
                return Yo(gi, {
                    hookProps: n,
                    content: t.content,
                    defaultContent: t.defaultContent,
                    backupElRef: e.rootElRef
                }, (function(e, o) {
                    return t.children(r, Ei(t.classNames, n), e, o)
                }
                ))
            }
            ))
        }
        ,
        t
    }(ii)
      , vi = $o(0);
    function gi(e) {
        return Yo(vi.Consumer, null, (function(t) {
            return Yo(mi, r({
                renderId: t
            }, e))
        }
        ))
    }
    var mi = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.innerElRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            return this.props.children(this.innerElRef, this.renderInnerContent())
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateCustomContent()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateCustomContent()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.customContentInfo && this.customContentInfo.destroy && this.customContentInfo.destroy()
        }
        ,
        t.prototype.renderInnerContent = function() {
            var e = this.customContentInfo
              , t = this.getInnerContent()
              , n = this.getContentMeta(t);
            return e && e.contentKey === n.contentKey ? e && (e.contentVal = t[n.contentKey]) : (e && (e.destroy && e.destroy(),
            e = this.customContentInfo = null),
            n.contentKey && (e = this.customContentInfo = r({
                contentKey: n.contentKey,
                contentVal: t[n.contentKey]
            }, n.buildLifecycleFuncs()))),
            e ? [] : t
        }
        ,
        t.prototype.getInnerContent = function() {
            var e = this.props
              , t = bi(e.content, e.hookProps);
            return void 0 === t && (t = bi(e.defaultContent, e.hookProps)),
            null == t ? null : t
        }
        ,
        t.prototype.getContentMeta = function(e) {
            var t = this.context.pluginHooks.contentTypeHandlers
              , n = ""
              , r = null;
            if (e)
                for (var o in t)
                    if (void 0 !== e[o]) {
                        n = o,
                        r = t[o];
                        break
                    }
            return {
                contentKey: n,
                buildLifecycleFuncs: r
            }
        }
        ,
        t.prototype.updateCustomContent = function() {
            this.customContentInfo && this.customContentInfo.render(this.innerElRef.current || this.props.backupElRef.current, this.customContentInfo.contentVal)
        }
        ,
        t
    }(ii)
      , yi = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.handleRootEl = function(e) {
                t.rootEl = e,
                t.props.elRef && li(t.props.elRef, e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            return this.props.children(this.handleRootEl)
        }
        ,
        t.prototype.componentDidMount = function() {
            var e = this.props.didMount;
            e && e(r(r({}, this.props.hookProps), {
                el: this.rootEl
            }))
        }
        ,
        t.prototype.componentWillUnmount = function() {
            var e = this.props.willUnmount;
            e && e(r(r({}, this.props.hookProps), {
                el: this.rootEl
            }))
        }
        ,
        t
    }(ii);
    function Si() {
        var e, t, n = [];
        return function(r, o) {
            return t && Wt(t, o) && r === e || (e = r,
            t = o,
            n = Ei(r, o)),
            n
        }
    }
    function Ei(e, t) {
        return "function" == typeof e && (e = e(t)),
        Gn(e)
    }
    function bi(e, t) {
        return "function" == typeof e ? e(t, Yo) : e
    }
    var Ci = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.normalizeClassNames = Si(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options
              , r = {
                view: t.viewApi
            }
              , o = this.normalizeClassNames(n.viewClassNames, r);
            return Yo(yi, {
                hookProps: r,
                didMount: n.viewDidMount,
                willUnmount: n.viewWillUnmount,
                elRef: e.elRef
            }, (function(t) {
                return e.children(t, ["fc-" + e.viewSpec.type + "-view", "fc-view"].concat(o))
            }
            ))
        }
        ,
        t
    }(ii);
    function Di(e) {
        return Ht(e, Ri)
    }
    function Ri(e) {
        var t, n = "function" == typeof e ? {
            component: e
        } : e, o = n.component;
        return n.content && (t = n,
        o = function(e) {
            return Yo(ni.Consumer, null, (function(n) {
                return Yo(Ci, {
                    viewSpec: n.viewSpec
                }, (function(o, i) {
                    var a = r(r({}, e), {
                        nextDayThreshold: n.options.nextDayThreshold
                    });
                    return Yo(hi, {
                        hookProps: a,
                        classNames: t.classNames,
                        content: t.content,
                        didMount: t.didMount,
                        willUnmount: t.willUnmount,
                        elRef: o
                    }, (function(e, t, n, r) {
                        return Yo("div", {
                            className: i.concat(t).join(" "),
                            ref: e
                        }, r)
                    }
                    ))
                }
                ))
            }
            ))
        }
        ),
        {
            superType: n.type,
            component: o,
            rawOptions: n
        }
    }
    function wi(e, t, n, o) {
        var i = Di(e)
          , a = Di(t.views);
        return Ht(function(e, t) {
            var n, r = {};
            for (n in e)
                fi(n, r, e, t);
            for (n in t)
                fi(n, r, e, t);
            return r
        }(i, a), (function(e) {
            return function(e, t, n, o, i) {
                var a = e.overrides.duration || e.defaults.duration || o.duration || n.duration
                  , s = null
                  , l = ""
                  , u = ""
                  , c = {};
                if (a && (s = function(e) {
                    var t = JSON.stringify(e)
                      , n = Ti[t];
                    void 0 === n && (n = qt(e),
                    Ti[t] = n);
                    return n
                }(a))) {
                    var d = nn(s);
                    l = d.unit,
                    1 === d.value && (u = l,
                    c = t[l] ? t[l].rawOptions : {})
                }
                var p = function(t) {
                    var n = t.buttonText || {}
                      , r = e.defaults.buttonTextKey;
                    return null != r && null != n[r] ? n[r] : null != n[e.type] ? n[e.type] : null != n[u] ? n[u] : null
                }
                  , f = function(t) {
                    var n = t.buttonHints || {}
                      , r = e.defaults.buttonTextKey;
                    return null != r && null != n[r] ? n[r] : null != n[e.type] ? n[e.type] : null != n[u] ? n[u] : null
                };
                return {
                    type: e.type,
                    component: e.component,
                    duration: s,
                    durationUnit: l,
                    singleUnit: u,
                    optionDefaults: e.defaults,
                    optionOverrides: r(r({}, c), e.overrides),
                    buttonTextOverride: p(o) || p(n) || e.overrides.buttonText,
                    buttonTextDefault: p(i) || e.defaults.buttonText || p(kn) || e.type,
                    buttonTitleOverride: f(o) || f(n) || e.overrides.buttonHint,
                    buttonTitleDefault: f(i) || e.defaults.buttonHint || f(kn)
                }
            }(e, a, t, n, o)
        }
        ))
    }
    var Ti = {};
    var _i = function() {
        function e(e) {
            this.props = e,
            this.nowDate = qr(e.nowInput, e.dateEnv),
            this.initHiddenDays()
        }
        return e.prototype.buildPrev = function(e, t, n) {
            var r = this.props.dateEnv
              , o = r.subtract(r.startOf(t, e.currentRangeUnit), e.dateIncrement);
            return this.build(o, -1, n)
        }
        ,
        e.prototype.buildNext = function(e, t, n) {
            var r = this.props.dateEnv
              , o = r.add(r.startOf(t, e.currentRangeUnit), e.dateIncrement);
            return this.build(o, 1, n)
        }
        ,
        e.prototype.build = function(e, t, n) {
            void 0 === n && (n = !0);
            var r, o, i, a, s, l, u, c, d = this.props;
            return r = this.buildValidRange(),
            r = this.trimHiddenDays(r),
            n && (u = e,
            e = null != (c = r).start && u < c.start ? c.start : null != c.end && u >= c.end ? new Date(c.end.valueOf() - 1) : u),
            o = this.buildCurrentRangeInfo(e, t),
            i = /^(year|month|week|day)$/.test(o.unit),
            a = this.buildRenderRange(this.trimHiddenDays(o.range), o.unit, i),
            s = a = this.trimHiddenDays(a),
            d.showNonCurrentDates || (s = ur(s, o.range)),
            s = ur(s = this.adjustActiveRange(s), r),
            l = dr(o.range, r),
            {
                validRange: r,
                currentRange: o.range,
                currentRangeUnit: o.unit,
                isRangeAllDay: i,
                activeRange: s,
                renderRange: a,
                slotMinTime: d.slotMinTime,
                slotMaxTime: d.slotMaxTime,
                isValid: l,
                dateIncrement: this.buildDateIncrement(o.duration)
            }
        }
        ,
        e.prototype.buildValidRange = function() {
            var e = this.props.validRangeInput
              , t = "function" == typeof e ? e.call(this.props.calendarApi, this.nowDate) : e;
            return this.refineRange(t) || {
                start: null,
                end: null
            }
        }
        ,
        e.prototype.buildCurrentRangeInfo = function(e, t) {
            var n, r = this.props, o = null, i = null, a = null;
            return r.duration ? (o = r.duration,
            i = r.durationUnit,
            a = this.buildRangeFromDuration(e, t, o, i)) : (n = this.props.dayCount) ? (i = "day",
            a = this.buildRangeFromDayCount(e, t, n)) : (a = this.buildCustomVisibleRange(e)) ? i = r.dateEnv.greatestWholeUnit(a.start, a.end).unit : (i = nn(o = this.getFallbackDuration()).unit,
            a = this.buildRangeFromDuration(e, t, o, i)),
            {
                duration: o,
                unit: i,
                range: a
            }
        }
        ,
        e.prototype.getFallbackDuration = function() {
            return qt({
                day: 1
            })
        }
        ,
        e.prototype.adjustActiveRange = function(e) {
            var t = this.props
              , n = t.dateEnv
              , r = t.usesMinMaxTime
              , o = t.slotMinTime
              , i = t.slotMaxTime
              , a = e.start
              , s = e.end;
            return r && ($t(o) < 0 && (a = bt(a),
            a = n.add(a, o)),
            $t(i) > 1 && (s = ht(s = bt(s), -1),
            s = n.add(s, i))),
            {
                start: a,
                end: s
            }
        }
        ,
        e.prototype.buildRangeFromDuration = function(e, t, n, r) {
            var o, i, a, s = this.props, l = s.dateEnv, u = s.dateAlignment;
            if (!u) {
                var c = this.props.dateIncrement;
                u = c && en(c) < en(n) ? nn(c).unit : r
            }
            function d() {
                o = l.startOf(e, u),
                i = l.add(o, n),
                a = {
                    start: o,
                    end: i
                }
            }
            return $t(n) <= 1 && this.isHiddenDay(o) && (o = bt(o = this.skipHiddenDays(o, t))),
            d(),
            this.trimHiddenDays(a) || (e = this.skipHiddenDays(e, t),
            d()),
            a
        }
        ,
        e.prototype.buildRangeFromDayCount = function(e, t, n) {
            var r, o = this.props, i = o.dateEnv, a = o.dateAlignment, s = 0, l = e;
            a && (l = i.startOf(l, a)),
            l = bt(l),
            r = l = this.skipHiddenDays(l, t);
            do {
                r = ht(r, 1),
                this.isHiddenDay(r) || (s += 1)
            } while (s < n);
            return {
                start: l,
                end: r
            }
        }
        ,
        e.prototype.buildCustomVisibleRange = function(e) {
            var t = this.props
              , n = t.visibleRangeInput
              , r = "function" == typeof n ? n.call(t.calendarApi, t.dateEnv.toDate(e)) : n
              , o = this.refineRange(r);
            return !o || null != o.start && null != o.end ? o : null
        }
        ,
        e.prototype.buildRenderRange = function(e, t, n) {
            return e
        }
        ,
        e.prototype.buildDateIncrement = function(e) {
            var t, n = this.props.dateIncrement;
            return n || ((t = this.props.dateAlignment) ? qt(1, t) : e || qt({
                days: 1
            }))
        }
        ,
        e.prototype.refineRange = function(e) {
            if (e) {
                var t = (n = e,
                r = this.props.dateEnv,
                o = null,
                i = null,
                n.start && (o = r.createMarker(n.start)),
                n.end && (i = r.createMarker(n.end)),
                o || i ? o && i && i < o ? null : {
                    start: o,
                    end: i
                } : null);
                return t && (t = or(t)),
                t
            }
            var n, r, o, i;
            return null
        }
        ,
        e.prototype.initHiddenDays = function() {
            var e, t = this.props.hiddenDays || [], n = [], r = 0;
            for (!1 === this.props.weekends && t.push(0, 6),
            e = 0; e < 7; e += 1)
                (n[e] = -1 !== t.indexOf(e)) || (r += 1);
            if (!r)
                throw new Error("invalid hiddenDays");
            this.isHiddenDayHash = n
        }
        ,
        e.prototype.trimHiddenDays = function(e) {
            var t = e.start
              , n = e.end;
            return t && (t = this.skipHiddenDays(t)),
            n && (n = this.skipHiddenDays(n, -1, !0)),
            null == t || null == n || t < n ? {
                start: t,
                end: n
            } : null
        }
        ,
        e.prototype.isHiddenDay = function(e) {
            return e instanceof Date && (e = e.getUTCDay()),
            this.isHiddenDayHash[e]
        }
        ,
        e.prototype.skipHiddenDays = function(e, t, n) {
            for (void 0 === t && (t = 1),
            void 0 === n && (n = !1); this.isHiddenDayHash[(e.getUTCDay() + (n ? t : 0) + 7) % 7]; )
                e = ht(e, t);
            return e
        }
        ,
        e
    }();
    function xi(e, t, n) {
        var r = t ? t.activeRange : null;
        return Ii({}, function(e, t) {
            var n = jr(t)
              , r = [].concat(e.eventSources || [])
              , o = [];
            e.initialEvents && r.unshift(e.initialEvents);
            e.events && r.unshift(e.events);
            for (var i = 0, a = r; i < a.length; i++) {
                var s = Gr(a[i], t, n);
                s && o.push(s)
            }
            return o
        }(e, n), r, n)
    }
    function ki(e, t, n, o) {
        var i, a, s = n ? n.activeRange : null;
        switch (t.type) {
        case "ADD_EVENT_SOURCES":
            return Ii(e, t.sources, s, o);
        case "REMOVE_EVENT_SOURCE":
            return i = e,
            a = t.sourceId,
            Nt(i, (function(e) {
                return e.sourceId !== a
            }
            ));
        case "PREV":
        case "NEXT":
        case "CHANGE_DATE":
        case "CHANGE_VIEW_TYPE":
            return n ? Pi(e, s, o) : e;
        case "FETCH_EVENT_SOURCES":
            return Ni(e, t.sourceIds ? Ot(t.sourceIds) : Oi(e, o), s, t.isRefetch || !1, o);
        case "RECEIVE_EVENTS":
        case "RECEIVE_EVENT_ERROR":
            return function(e, t, n, o) {
                var i, a = e[t];
                if (a && n === a.latestFetchId)
                    return r(r({}, e), ((i = {})[t] = r(r({}, a), {
                        isFetching: !1,
                        fetchRange: o
                    }),
                    i));
                return e
            }(e, t.sourceId, t.fetchId, t.fetchRange);
        case "REMOVE_ALL_EVENT_SOURCES":
            return {};
        default:
            return e
        }
    }
    function Mi(e) {
        for (var t in e)
            if (e[t].isFetching)
                return !0;
        return !1
    }
    function Ii(e, t, n, o) {
        for (var i = {}, a = 0, s = t; a < s.length; a++) {
            var l = s[a];
            i[l.sourceId] = l
        }
        return n && (i = Pi(i, n, o)),
        r(r({}, e), i)
    }
    function Pi(e, t, n) {
        return Ni(e, Nt(e, (function(e) {
            return function(e, t, n) {
                if (!Ai(e, n))
                    return !e.latestFetchId;
                return !n.options.lazyFetching || !e.fetchRange || e.isFetching || t.start < e.fetchRange.start || t.end > e.fetchRange.end
            }(e, t, n)
        }
        )), t, !1, n)
    }
    function Ni(e, t, n, r, o) {
        var i = {};
        for (var a in e) {
            var s = e[a];
            t[a] ? i[a] = Hi(s, n, r, o) : i[a] = s
        }
        return i
    }
    function Hi(e, t, n, o) {
        var i = o.options
          , a = o.calendarApi
          , s = o.pluginHooks.eventSourceDefs[e.sourceDefId]
          , l = Ke();
        return s.fetch({
            eventSource: e,
            range: t,
            isRefetch: n,
            context: o
        }, (function(n) {
            var r = n.rawEvents;
            i.eventSourceSuccess && (r = i.eventSourceSuccess.call(a, r, n.xhr) || r),
            e.success && (r = e.success.call(a, r, n.xhr) || r),
            o.dispatch({
                type: "RECEIVE_EVENTS",
                sourceId: e.sourceId,
                fetchId: l,
                fetchRange: t,
                rawEvents: r
            })
        }
        ), (function(n) {
            console.warn(n.message, n),
            i.eventSourceFailure && i.eventSourceFailure.call(a, n),
            e.failure && e.failure(n),
            o.dispatch({
                type: "RECEIVE_EVENT_ERROR",
                sourceId: e.sourceId,
                fetchId: l,
                fetchRange: t,
                error: n
            })
        }
        )),
        r(r({}, e), {
            isFetching: !0,
            latestFetchId: l
        })
    }
    function Oi(e, t) {
        return Nt(e, (function(e) {
            return Ai(e, t)
        }
        ))
    }
    function Ai(e, t) {
        return !t.pluginHooks.eventSourceDefs[e.sourceDefId].ignoreRange
    }
    function Wi(e, t, n, r, o) {
        switch (t.type) {
        case "RECEIVE_EVENTS":
            return function(e, t, n, r, o, i) {
                if (t && n === t.latestFetchId) {
                    var a = Ln(function(e, t, n) {
                        var r = n.options.eventDataTransform
                          , o = t ? t.eventDataTransform : null;
                        o && (e = Li(e, o));
                        r && (e = Li(e, r));
                        return e
                    }(o, t, i), t, i);
                    return r && (a = Vt(a, r, i)),
                    Vn(Ui(e, t.sourceId), a)
                }
                return e
            }(e, n[t.sourceId], t.fetchId, t.fetchRange, t.rawEvents, o);
        case "ADD_EVENTS":
            return function(e, t, n, r) {
                n && (t = Vt(t, n, r));
                return Vn(e, t)
            }(e, t.eventStore, r ? r.activeRange : null, o);
        case "RESET_EVENTS":
            return t.eventStore;
        case "MERGE_EVENTS":
            return Vn(e, t.eventStore);
        case "PREV":
        case "NEXT":
        case "CHANGE_DATE":
        case "CHANGE_VIEW_TYPE":
            return r ? Vt(e, r.activeRange, o) : e;
        case "REMOVE_EVENTS":
            return function(e, t) {
                var n = e.defs
                  , r = e.instances
                  , o = {}
                  , i = {};
                for (var a in n)
                    t.defs[a] || (o[a] = n[a]);
                for (var s in r)
                    !t.instances[s] && o[r[s].defId] && (i[s] = r[s]);
                return {
                    defs: o,
                    instances: i
                }
            }(e, t.eventStore);
        case "REMOVE_EVENT_SOURCE":
            return Ui(e, t.sourceId);
        case "REMOVE_ALL_EVENT_SOURCES":
            return Fn(e, (function(e) {
                return !e.sourceId
            }
            ));
        case "REMOVE_ALL_EVENTS":
            return {
                defs: {},
                instances: {}
            };
        default:
            return e
        }
    }
    function Li(e, t) {
        var n;
        if (t) {
            n = [];
            for (var r = 0, o = e; r < o.length; r++) {
                var i = o[r]
                  , a = t(i);
                a ? n.push(a) : null == a && n.push(i)
            }
        } else
            n = e;
        return n
    }
    function Ui(e, t) {
        return Fn(e, (function(e) {
            return e.sourceId !== t
        }
        ))
    }
    function Bi(e, t) {
        switch (t.type) {
        case "UNSELECT_DATES":
            return null;
        case "SELECT_DATES":
            return t.selection;
        default:
            return e
        }
    }
    function zi(e, t) {
        switch (t.type) {
        case "UNSELECT_EVENT":
            return "";
        case "SELECT_EVENT":
            return t.eventInstanceId;
        default:
            return e
        }
    }
    function Vi(e, t) {
        var n;
        switch (t.type) {
        case "UNSET_EVENT_DRAG":
            return null;
        case "SET_EVENT_DRAG":
            return {
                affectedEvents: (n = t.state).affectedEvents,
                mutatedEvents: n.mutatedEvents,
                isEvent: n.isEvent
            };
        default:
            return e
        }
    }
    function Fi(e, t) {
        var n;
        switch (t.type) {
        case "UNSET_EVENT_RESIZE":
            return null;
        case "SET_EVENT_RESIZE":
            return {
                affectedEvents: (n = t.state).affectedEvents,
                mutatedEvents: n.mutatedEvents,
                isEvent: n.isEvent
            };
        default:
            return e
        }
    }
    function Gi(e, t, n, r, o) {
        return {
            header: e.headerToolbar ? ji(e.headerToolbar, e, t, n, r, o) : null,
            footer: e.footerToolbar ? ji(e.footerToolbar, e, t, n, r, o) : null
        }
    }
    function ji(e, t, n, r, o, i) {
        var a = {}
          , s = []
          , l = !1;
        for (var u in e) {
            var c = qi(e[u], t, n, r, o, i);
            a[u] = c.widgets,
            s.push.apply(s, c.viewsWithButtons),
            l = l || c.hasTitle
        }
        return {
            sectionWidgets: a,
            viewsWithButtons: s,
            hasTitle: l
        }
    }
    function qi(e, t, n, r, o, i) {
        var a = "rtl" === t.direction
          , s = t.customButtons || {}
          , l = n.buttonText || {}
          , u = t.buttonText || {}
          , c = n.buttonHints || {}
          , d = t.buttonHints || {}
          , p = e ? e.split(" ") : []
          , f = []
          , h = !1;
        return {
            widgets: p.map((function(e) {
                return e.split(",").map((function(e) {
                    if ("title" === e)
                        return h = !0,
                        {
                            buttonName: e
                        };
                    var n, p, v, g, m, y;
                    if (n = s[e])
                        v = function(e) {
                            n.click && n.click.call(e.target, e, e.target)
                        }
                        ,
                        (g = r.getCustomButtonIconClass(n)) || (g = r.getIconClass(e, a)) || (m = n.text),
                        y = n.hint || n.text;
                    else if (p = o[e]) {
                        f.push(e),
                        v = function() {
                            i.changeView(e)
                        }
                        ,
                        (m = p.buttonTextOverride) || (g = r.getIconClass(e, a)) || (m = p.buttonTextDefault);
                        var S = p.buttonTextOverride || p.buttonTextDefault;
                        y = lt(p.buttonTitleOverride || p.buttonTitleDefault || t.viewHint, [S, e], S)
                    } else if (i[e])
                        if (v = function() {
                            i[e]()
                        }
                        ,
                        (m = l[e]) || (g = r.getIconClass(e, a)) || (m = u[e]),
                        "prevYear" === e || "nextYear" === e) {
                            var E = "prevYear" === e ? "prev" : "next";
                            y = lt(c[E] || d[E], [u.year || "year", "year"], u[e])
                        } else
                            y = function(t) {
                                return lt(c[e] || d[e], [u[t] || t, t], u[e])
                            }
                            ;
                    return {
                        buttonName: e,
                        buttonClick: v,
                        buttonIcon: g,
                        buttonText: m,
                        buttonHint: y
                    }
                }
                ))
            }
            )),
            viewsWithButtons: f,
            hasTitle: h
        }
    }
    function Yi(e, t, n, r, o) {
        var i = null;
        "GET" === (e = e.toUpperCase()) ? t = function(e, t) {
            return e + (-1 === e.indexOf("?") ? "?" : "&") + Zi(t)
        }(t, n) : i = Zi(n);
        var a = new XMLHttpRequest;
        a.open(e, t, !0),
        "GET" !== e && a.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"),
        a.onload = function() {
            if (a.status >= 200 && a.status < 400) {
                var e = !1
                  , t = void 0;
                try {
                    t = JSON.parse(a.responseText),
                    e = !0
                } catch (e) {}
                e ? r(t, a) : o("Failure parsing JSON", a)
            } else
                o("Request failed", a)
        }
        ,
        a.onerror = function() {
            o("Request failed", a)
        }
        ,
        a.send(i)
    }
    function Zi(e) {
        var t = [];
        for (var n in e)
            t.push(encodeURIComponent(n) + "=" + encodeURIComponent(e[n]));
        return t.join("&")
    }
    function Xi(e, t) {
        for (var n = At(t.getCurrentData().eventSources), r = [], o = 0, i = e; o < i.length; o++) {
            for (var a = i[o], s = !1, l = 0; l < n.length; l += 1)
                if (n[l]._raw === a) {
                    n.splice(l, 1),
                    s = !0;
                    break
                }
            s || r.push(a)
        }
        for (var u = 0, c = n; u < c.length; u++) {
            var d = c[u];
            t.dispatch({
                type: "REMOVE_EVENT_SOURCE",
                sourceId: d.sourceId
            })
        }
        for (var p = 0, f = r; p < f.length; p++) {
            var h = f[p];
            t.calendarApi.addEventSource(h)
        }
    }
    var Ki = [ci({
        eventSourceDefs: [{
            ignoreRange: !0,
            parseMeta: function(e) {
                return Array.isArray(e.events) ? e.events : null
            },
            fetch: function(e, t) {
                t({
                    rawEvents: e.eventSource.meta
                })
            }
        }]
    }), ci({
        eventSourceDefs: [{
            parseMeta: function(e) {
                return "function" == typeof e.events ? e.events : null
            },
            fetch: function(e, t, n) {
                var r = e.context.dateEnv;
                Uo(e.eventSource.meta.bind(null, Nr(e.range, r)), (function(e) {
                    t({
                        rawEvents: e
                    })
                }
                ), n)
            }
        }]
    }), ci({
        eventSourceRefiners: {
            method: String,
            extraParams: Wn,
            startParam: String,
            endParam: String,
            timeZoneParam: String
        },
        eventSourceDefs: [{
            parseMeta: function(e) {
                return !e.url || "json" !== e.format && e.format ? null : {
                    url: e.url,
                    format: "json",
                    method: (e.method || "GET").toUpperCase(),
                    extraParams: e.extraParams,
                    startParam: e.startParam,
                    endParam: e.endParam,
                    timeZoneParam: e.timeZoneParam
                }
            },
            fetch: function(e, t, n) {
                var o = e.eventSource.meta
                  , i = function(e, t, n) {
                    var o, i, a, s, l = n.dateEnv, u = n.options, c = {};
                    null == (o = e.startParam) && (o = u.startParam);
                    null == (i = e.endParam) && (i = u.endParam);
                    null == (a = e.timeZoneParam) && (a = u.timeZoneParam);
                    s = "function" == typeof e.extraParams ? e.extraParams() : e.extraParams || {};
                    r(c, s),
                    c[o] = l.formatIso(t.start),
                    c[i] = l.formatIso(t.end),
                    "local" !== l.timeZone && (c[a] = l.timeZone);
                    return c
                }(o, e.range, e.context);
                Yi(o.method, o.url, i, (function(e, n) {
                    t({
                        rawEvents: e,
                        xhr: n
                    })
                }
                ), (function(e, t) {
                    n({
                        message: e,
                        xhr: t
                    })
                }
                ))
            }
        }]
    }), ci({
        recurringTypes: [{
            parse: function(e, t) {
                if (e.daysOfWeek || e.startTime || e.endTime || e.startRecur || e.endRecur) {
                    var n = {
                        daysOfWeek: e.daysOfWeek || null,
                        startTime: e.startTime || null,
                        endTime: e.endTime || null,
                        startRecur: e.startRecur ? t.createMarker(e.startRecur) : null,
                        endRecur: e.endRecur ? t.createMarker(e.endRecur) : null
                    }
                      , r = void 0;
                    return e.duration && (r = e.duration),
                    !r && e.startTime && e.endTime && (o = e.endTime,
                    i = e.startTime,
                    r = {
                        years: o.years - i.years,
                        months: o.months - i.months,
                        days: o.days - i.days,
                        milliseconds: o.milliseconds - i.milliseconds
                    }),
                    {
                        allDayGuess: Boolean(!e.startTime && !e.endTime),
                        duration: r,
                        typeData: n
                    }
                }
                var o, i;
                return null
            },
            expand: function(e, t, n) {
                var r = ur(t, {
                    start: e.startRecur,
                    end: e.endRecur
                });
                return r ? function(e, t, n, r) {
                    var o = e ? Ot(e) : null
                      , i = bt(n.start)
                      , a = n.end
                      , s = [];
                    for (; i < a; ) {
                        var l = void 0;
                        o && !o[i.getUTCDay()] || (l = t ? r.add(i, t) : i,
                        s.push(l)),
                        i = ht(i, 1)
                    }
                    return s
                }(e.daysOfWeek, e.startTime, r, n) : []
            }
        }],
        eventRefiners: {
            daysOfWeek: Wn,
            startTime: qt,
            endTime: qt,
            duration: qt,
            startRecur: Wn,
            endRecur: Wn
        }
    }), ci({
        optionChangeHandlers: {
            events: function(e, t) {
                Xi([e], t)
            },
            eventSources: Xi
        }
    }), ci({
        isLoadingFuncs: [function(e) {
            return Mi(e.eventSources)
        }
        ],
        contentTypeHandlers: {
            html: function() {
                var e = null
                  , t = "";
                return {
                    render: function(n, r) {
                        n === e && r === t || (n.innerHTML = r),
                        e = n,
                        t = r
                    },
                    destroy: function() {
                        e.innerHTML = "",
                        e = null,
                        t = ""
                    }
                }
            },
            domNodes: function() {
                var e = null
                  , t = [];
                function n() {
                    t.forEach(Ie),
                    t = [],
                    e = null
                }
                return {
                    render: function(r, o) {
                        var i = Array.prototype.slice.call(o);
                        if (r !== e || !un(t, i)) {
                            for (var a = 0, s = i; a < s.length; a++) {
                                var l = s[a];
                                r.appendChild(l)
                            }
                            n()
                        }
                        e = r,
                        t = i
                    },
                    destroy: n
                }
            }
        },
        propSetHandlers: {
            dateProfile: function(e, t) {
                t.emitter.trigger("datesSet", r(r({}, Nr(e.activeRange, t.dateEnv)), {
                    view: t.viewApi
                }))
            },
            eventStore: function(e, t) {
                var n = t.emitter;
                n.hasHandlers("eventsSet") && n.trigger("eventsSet", Kr(e, t))
            }
        }
    })];
    var $i = function() {
        function e(e) {
            this.drainedOption = e,
            this.isRunning = !1,
            this.isDirty = !1,
            this.pauseDepths = {},
            this.timeoutId = 0
        }
        return e.prototype.request = function(e) {
            this.isDirty = !0,
            this.isPaused() || (this.clearTimeout(),
            null == e ? this.tryDrain() : this.timeoutId = setTimeout(this.tryDrain.bind(this), e))
        }
        ,
        e.prototype.pause = function(e) {
            void 0 === e && (e = "");
            var t = this.pauseDepths;
            t[e] = (t[e] || 0) + 1,
            this.clearTimeout()
        }
        ,
        e.prototype.resume = function(e, t) {
            void 0 === e && (e = "");
            var n = this.pauseDepths;
            if (e in n) {
                if (t)
                    delete n[e];
                else
                    n[e] -= 1,
                    n[e] <= 0 && delete n[e];
                this.tryDrain()
            }
        }
        ,
        e.prototype.isPaused = function() {
            return Object.keys(this.pauseDepths).length
        }
        ,
        e.prototype.tryDrain = function() {
            if (!this.isRunning && !this.isPaused()) {
                for (this.isRunning = !0; this.isDirty; )
                    this.isDirty = !1,
                    this.drained();
                this.isRunning = !1
            }
        }
        ,
        e.prototype.clear = function() {
            this.clearTimeout(),
            this.isDirty = !1,
            this.pauseDepths = {}
        }
        ,
        e.prototype.clearTimeout = function() {
            this.timeoutId && (clearTimeout(this.timeoutId),
            this.timeoutId = 0)
        }
        ,
        e.prototype.drained = function() {
            this.drainedOption && this.drainedOption()
        }
        ,
        e
    }()
      , Ji = function() {
        function e(e, t) {
            this.runTaskOption = e,
            this.drainedOption = t,
            this.queue = [],
            this.delayedRunner = new $i(this.drain.bind(this))
        }
        return e.prototype.request = function(e, t) {
            this.queue.push(e),
            this.delayedRunner.request(t)
        }
        ,
        e.prototype.pause = function(e) {
            this.delayedRunner.pause(e)
        }
        ,
        e.prototype.resume = function(e, t) {
            this.delayedRunner.resume(e, t)
        }
        ,
        e.prototype.drain = function() {
            for (var e = this.queue; e.length; ) {
                for (var t = [], n = void 0; n = e.shift(); )
                    this.runTask(n),
                    t.push(n);
                this.drained(t)
            }
        }
        ,
        e.prototype.runTask = function(e) {
            this.runTaskOption && this.runTaskOption(e)
        }
        ,
        e.prototype.drained = function(e) {
            this.drainedOption && this.drainedOption(e)
        }
        ,
        e
    }();
    function Qi(e, t, n) {
        var r;
        return r = /^(year|month)$/.test(e.currentRangeUnit) ? e.currentRange : e.activeRange,
        n.formatRange(r.start, r.end, _n(t.titleFormat || function(e) {
            var t = e.currentRangeUnit;
            if ("year" === t)
                return {
                    year: "numeric"
                };
            if ("month" === t)
                return {
                    year: "numeric",
                    month: "long"
                };
            var n = Et(e.currentRange.start, e.currentRange.end);
            if (null !== n && n > 1)
                return {
                    year: "numeric",
                    month: "short",
                    day: "numeric"
                };
            return {
                year: "numeric",
                month: "long",
                day: "numeric"
            }
        }(e)), {
            isEndExclusive: e.isRangeAllDay,
            defaultSeparator: t.titleRangeSeparator
        })
    }
    var ea = function() {
        function e(e) {
            var t = this;
            this.computeOptionsData = cn(this._computeOptionsData),
            this.computeCurrentViewData = cn(this._computeCurrentViewData),
            this.organizeRawLocales = cn(ao),
            this.buildLocale = cn(so),
            this.buildPluginHooks = di(),
            this.buildDateEnv = cn(ta),
            this.buildTheme = cn(na),
            this.parseToolbars = cn(Gi),
            this.buildViewSpecs = cn(wi),
            this.buildDateProfileGenerator = dn(ra),
            this.buildViewApi = cn(oa),
            this.buildViewUiProps = dn(sa),
            this.buildEventUiBySource = cn(ia, Wt),
            this.buildEventUiBases = cn(aa),
            this.parseContextBusinessHours = dn(ua),
            this.buildTitle = cn(Qi),
            this.emitter = new Bo,
            this.actionRunner = new Ji(this._handleAction.bind(this),this.updateData.bind(this)),
            this.currentCalendarOptionsInput = {},
            this.currentCalendarOptionsRefined = {},
            this.currentViewOptionsInput = {},
            this.currentViewOptionsRefined = {},
            this.currentCalendarOptionsRefiners = {},
            this.getCurrentData = function() {
                return t.data
            }
            ,
            this.dispatch = function(e) {
                t.actionRunner.request(e)
            }
            ,
            this.props = e,
            this.actionRunner.pause();
            var n = {}
              , o = this.computeOptionsData(e.optionOverrides, n, e.calendarApi)
              , i = o.calendarOptions.initialView || o.pluginHooks.initialView
              , a = this.computeCurrentViewData(i, o, e.optionOverrides, n);
            e.calendarApi.currentDataManager = this,
            this.emitter.setThisContext(e.calendarApi),
            this.emitter.setOptions(a.options);
            var s, l, u, c = (s = o.calendarOptions,
            l = o.dateEnv,
            null != (u = s.initialDate) ? l.createMarker(u) : qr(s.now, l)), d = a.dateProfileGenerator.build(c);
            fr(d.activeRange, c) || (c = d.currentRange.start);
            for (var p = {
                dateEnv: o.dateEnv,
                options: o.calendarOptions,
                pluginHooks: o.pluginHooks,
                calendarApi: e.calendarApi,
                dispatch: this.dispatch,
                emitter: this.emitter,
                getCurrentData: this.getCurrentData
            }, f = 0, h = o.pluginHooks.contextInit; f < h.length; f++) {
                (0,
                h[f])(p)
            }
            for (var v = xi(o.calendarOptions, d, p), g = {
                dynamicOptionOverrides: n,
                currentViewType: i,
                currentDate: c,
                dateProfile: d,
                businessHours: this.parseContextBusinessHours(p),
                eventSources: v,
                eventUiBases: {},
                eventStore: {
                    defs: {},
                    instances: {}
                },
                renderableEventStore: {
                    defs: {},
                    instances: {}
                },
                dateSelection: null,
                eventSelection: "",
                eventDrag: null,
                eventResize: null,
                selectionConfig: this.buildViewUiProps(p).selectionConfig
            }, m = r(r({}, p), g), y = 0, S = o.pluginHooks.reducers; y < S.length; y++) {
                var E = S[y];
                r(g, E(null, null, m))
            }
            la(g, p) && this.emitter.trigger("loading", !0),
            this.state = g,
            this.updateData(),
            this.actionRunner.resume()
        }
        return e.prototype.resetOptions = function(e, t) {
            var n = this.props;
            n.optionOverrides = t ? r(r({}, n.optionOverrides), e) : e,
            this.actionRunner.request({
                type: "NOTHING"
            })
        }
        ,
        e.prototype._handleAction = function(e) {
            var t = this
              , n = t.props
              , o = t.state
              , i = t.emitter
              , a = function(e, t) {
                var n;
                switch (t.type) {
                case "SET_OPTION":
                    return r(r({}, e), ((n = {})[t.optionName] = t.rawOptionValue,
                    n));
                default:
                    return e
                }
            }(o.dynamicOptionOverrides, e)
              , s = this.computeOptionsData(n.optionOverrides, a, n.calendarApi)
              , l = function(e, t) {
                switch (t.type) {
                case "CHANGE_VIEW_TYPE":
                    e = t.viewType
                }
                return e
            }(o.currentViewType, e)
              , u = this.computeCurrentViewData(l, s, n.optionOverrides, a);
            n.calendarApi.currentDataManager = this,
            i.setThisContext(n.calendarApi),
            i.setOptions(u.options);
            var c = {
                dateEnv: s.dateEnv,
                options: s.calendarOptions,
                pluginHooks: s.pluginHooks,
                calendarApi: n.calendarApi,
                dispatch: this.dispatch,
                emitter: i,
                getCurrentData: this.getCurrentData
            }
              , d = o.currentDate
              , p = o.dateProfile;
            this.data && this.data.dateProfileGenerator !== u.dateProfileGenerator && (p = u.dateProfileGenerator.build(d)),
            p = function(e, t, n, r) {
                var o;
                switch (t.type) {
                case "CHANGE_VIEW_TYPE":
                    return r.build(t.dateMarker || n);
                case "CHANGE_DATE":
                    return r.build(t.dateMarker);
                case "PREV":
                    if ((o = r.buildPrev(e, n)).isValid)
                        return o;
                    break;
                case "NEXT":
                    if ((o = r.buildNext(e, n)).isValid)
                        return o
                }
                return e
            }(p, e, d = function(e, t) {
                switch (t.type) {
                case "CHANGE_DATE":
                    return t.dateMarker;
                default:
                    return e
                }
            }(d, e), u.dateProfileGenerator),
            "PREV" !== e.type && "NEXT" !== e.type && fr(p.currentRange, d) || (d = p.currentRange.start);
            for (var f = ki(o.eventSources, e, p, c), h = Wi(o.eventStore, e, f, p, c), v = Mi(f) && !u.options.progressiveEventRendering && o.renderableEventStore || h, g = this.buildViewUiProps(c), m = g.eventUiSingleBase, y = g.selectionConfig, S = this.buildEventUiBySource(f), E = {
                dynamicOptionOverrides: a,
                currentViewType: l,
                currentDate: d,
                dateProfile: p,
                eventSources: f,
                eventStore: h,
                renderableEventStore: v,
                selectionConfig: y,
                eventUiBases: this.buildEventUiBases(v.defs, m, S),
                businessHours: this.parseContextBusinessHours(c),
                dateSelection: Bi(o.dateSelection, e),
                eventSelection: zi(o.eventSelection, e),
                eventDrag: Vi(o.eventDrag, e),
                eventResize: Fi(o.eventResize, e)
            }, b = r(r({}, c), E), C = 0, D = s.pluginHooks.reducers; C < D.length; C++) {
                var R = D[C];
                r(E, R(o, e, b))
            }
            var w = la(o, c)
              , T = la(E, c);
            !w && T ? i.trigger("loading", !0) : w && !T && i.trigger("loading", !1),
            this.state = E,
            n.onAction && n.onAction(e)
        }
        ,
        e.prototype.updateData = function() {
            var e, t, n, o, i = this.props, a = this.state, s = this.data, l = this.computeOptionsData(i.optionOverrides, a.dynamicOptionOverrides, i.calendarApi), u = this.computeCurrentViewData(a.currentViewType, l, i.optionOverrides, a.dynamicOptionOverrides), c = this.data = r(r(r({
                viewTitle: this.buildTitle(a.dateProfile, u.options, l.dateEnv),
                calendarApi: i.calendarApi,
                dispatch: this.dispatch,
                emitter: this.emitter,
                getCurrentData: this.getCurrentData
            }, l), u), a), d = l.pluginHooks.optionChangeHandlers, p = s && s.calendarOptions, f = l.calendarOptions;
            if (p && p !== f)
                for (var h in p.timeZone !== f.timeZone && (a.eventSources = c.eventSources = (e = c.eventSources,
                t = a.dateProfile,
                n = c,
                o = t ? t.activeRange : null,
                Ni(e, Oi(e, n), o, !0, n)),
                a.eventStore = c.eventStore = function(e, t, n) {
                    var o = e.defs
                      , i = Ht(e.instances, (function(e) {
                        var i = o[e.defId];
                        return i.allDay || i.recurringDef ? e : r(r({}, e), {
                            range: {
                                start: n.createMarker(t.toDate(e.range.start, e.forcedStartTzo)),
                                end: n.createMarker(t.toDate(e.range.end, e.forcedEndTzo))
                            },
                            forcedStartTzo: n.canComputeOffset ? null : e.forcedStartTzo,
                            forcedEndTzo: n.canComputeOffset ? null : e.forcedEndTzo
                        })
                    }
                    ));
                    return {
                        defs: o,
                        instances: i
                    }
                }(c.eventStore, s.dateEnv, c.dateEnv)),
                d)
                    p[h] !== f[h] && d[h](f[h], c);
            i.onData && i.onData(c)
        }
        ,
        e.prototype._computeOptionsData = function(e, t, n) {
            var r = this.processRawCalendarOptions(e, t)
              , o = r.refinedOptions
              , i = r.pluginHooks
              , a = r.localeDefaults
              , s = r.availableLocaleData;
            ca(r.extra);
            var l = this.buildDateEnv(o.timeZone, o.locale, o.weekNumberCalculation, o.firstDay, o.weekText, i, s, o.defaultRangeSeparator)
              , u = this.buildViewSpecs(i.views, e, t, a)
              , c = this.buildTheme(o, i);
            return {
                calendarOptions: o,
                pluginHooks: i,
                dateEnv: l,
                viewSpecs: u,
                theme: c,
                toolbarConfig: this.parseToolbars(o, e, c, u, n),
                localeDefaults: a,
                availableRawLocales: s.map
            }
        }
        ,
        e.prototype.processRawCalendarOptions = function(e, t) {
            var n = On([kn, e, t])
              , o = n.locales
              , i = n.locale
              , a = this.organizeRawLocales(o)
              , s = a.map
              , l = this.buildLocale(i || a.defaultCode, s).options
              , u = this.buildPluginHooks(e.plugins || [], Ki)
              , c = this.currentCalendarOptionsRefiners = r(r(r(r(r({}, xn), Mn), In), u.listenerRefiners), u.optionRefiners)
              , d = {}
              , p = On([kn, l, e, t])
              , f = {}
              , h = this.currentCalendarOptionsInput
              , v = this.currentCalendarOptionsRefined
              , g = !1;
            for (var m in p)
                "plugins" !== m && (p[m] === h[m] || Pn[m] && m in h && Pn[m](h[m], p[m]) ? f[m] = v[m] : c[m] ? (f[m] = c[m](p[m]),
                g = !0) : d[m] = h[m]);
            return g && (this.currentCalendarOptionsInput = p,
            this.currentCalendarOptionsRefined = f),
            {
                rawOptions: this.currentCalendarOptionsInput,
                refinedOptions: this.currentCalendarOptionsRefined,
                pluginHooks: u,
                availableLocaleData: a,
                localeDefaults: l,
                extra: d
            }
        }
        ,
        e.prototype._computeCurrentViewData = function(e, t, n, r) {
            var o = t.viewSpecs[e];
            if (!o)
                throw new Error('viewType "' + e + "\" is not available. Please make sure you've loaded all neccessary plugins");
            var i = this.processRawViewOptions(o, t.pluginHooks, t.localeDefaults, n, r)
              , a = i.refinedOptions;
            return ca(i.extra),
            {
                viewSpec: o,
                options: a,
                dateProfileGenerator: this.buildDateProfileGenerator({
                    dateProfileGeneratorClass: o.optionDefaults.dateProfileGeneratorClass,
                    duration: o.duration,
                    durationUnit: o.durationUnit,
                    usesMinMaxTime: o.optionDefaults.usesMinMaxTime,
                    dateEnv: t.dateEnv,
                    calendarApi: this.props.calendarApi,
                    slotMinTime: a.slotMinTime,
                    slotMaxTime: a.slotMaxTime,
                    showNonCurrentDates: a.showNonCurrentDates,
                    dayCount: a.dayCount,
                    dateAlignment: a.dateAlignment,
                    dateIncrement: a.dateIncrement,
                    hiddenDays: a.hiddenDays,
                    weekends: a.weekends,
                    nowInput: a.now,
                    validRangeInput: a.validRange,
                    visibleRangeInput: a.visibleRange,
                    monthMode: a.monthMode,
                    fixedWeekCount: a.fixedWeekCount
                }),
                viewApi: this.buildViewApi(e, this.getCurrentData, t.dateEnv)
            }
        }
        ,
        e.prototype.processRawViewOptions = function(e, t, n, o, i) {
            var a = On([kn, e.optionDefaults, n, o, e.optionOverrides, i])
              , s = r(r(r(r(r(r({}, xn), Mn), In), Hn), t.listenerRefiners), t.optionRefiners)
              , l = {}
              , u = this.currentViewOptionsInput
              , c = this.currentViewOptionsRefined
              , d = !1
              , p = {};
            for (var f in a)
                a[f] === u[f] || Pn[f] && Pn[f](a[f], u[f]) ? l[f] = c[f] : (a[f] === this.currentCalendarOptionsInput[f] || Pn[f] && Pn[f](a[f], this.currentCalendarOptionsInput[f]) ? f in this.currentCalendarOptionsRefined && (l[f] = this.currentCalendarOptionsRefined[f]) : s[f] ? l[f] = s[f](a[f]) : p[f] = a[f],
                d = !0);
            return d && (this.currentViewOptionsInput = a,
            this.currentViewOptionsRefined = l),
            {
                rawOptions: this.currentViewOptionsInput,
                refinedOptions: this.currentViewOptionsRefined,
                extra: p
            }
        }
        ,
        e
    }();
    function ta(e, t, n, r, o, i, a, s) {
        var l = so(t || a.defaultCode, a.map);
        return new no({
            calendarSystem: "gregory",
            timeZone: e,
            namedTimeZoneImpl: i.namedTimeZonedImpl,
            locale: l,
            weekNumberCalculation: n,
            firstDay: r,
            weekText: o,
            cmdFormatter: i.cmdFormatter,
            defaultSeparator: s
        })
    }
    function na(e, t) {
        return new (t.themeClasses[e.themeSystem] || pi)(e)
    }
    function ra(e) {
        return new (e.dateProfileGeneratorClass || _i)(e)
    }
    function oa(e, t, n) {
        return new Vr(e,t,n)
    }
    function ia(e) {
        return Ht(e, (function(e) {
            return e.ui
        }
        ))
    }
    function aa(e, t, n) {
        var r = {
            "": t
        };
        for (var o in e) {
            var i = e[o];
            i.sourceId && n[i.sourceId] && (r[o] = n[i.sourceId])
        }
        return r
    }
    function sa(e) {
        var t = e.options;
        return {
            eventUiSingleBase: Yn({
                display: t.eventDisplay,
                editable: t.editable,
                startEditable: t.eventStartEditable,
                durationEditable: t.eventDurationEditable,
                constraint: t.eventConstraint,
                overlap: "boolean" == typeof t.eventOverlap ? t.eventOverlap : void 0,
                allow: t.eventAllow,
                backgroundColor: t.eventBackgroundColor,
                borderColor: t.eventBorderColor,
                textColor: t.eventTextColor,
                color: t.eventColor
            }, e),
            selectionConfig: Yn({
                constraint: t.selectConstraint,
                overlap: "boolean" == typeof t.selectOverlap ? t.selectOverlap : void 0,
                allow: t.selectAllow
            }, e)
        }
    }
    function la(e, t) {
        for (var n = 0, r = t.pluginHooks.isLoadingFuncs; n < r.length; n++) {
            if ((0,
            r[n])(e))
                return !0
        }
        return !1
    }
    function ua(e) {
        return fo(e.options.businessHours, e)
    }
    function ca(e, t) {
        for (var n in e)
            console.warn("Unknown option '" + n + "'" + (t ? " for view '" + t + "'" : ""))
    }
    var da = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            return n.handleData = function(e) {
                n.dataManager ? n.setState(e) : n.state = e
            }
            ,
            n.dataManager = new ea({
                optionOverrides: t.optionOverrides,
                calendarApi: t.calendarApi,
                onData: n.handleData
            }),
            n
        }
        return n(t, e),
        t.prototype.render = function() {
            return this.props.children(this.state)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            var t = this.props.optionOverrides;
            t !== e.optionOverrides && this.dataManager.resetOptions(t)
        }
        ,
        t
    }(qo);
    var pa = function(e) {
        this.timeZoneName = e
    }
      , fa = function() {
        function e() {
            this.strictOrder = !1,
            this.allowReslicing = !1,
            this.maxCoord = -1,
            this.maxStackCnt = -1,
            this.levelCoords = [],
            this.entriesByLevel = [],
            this.stackCnts = {}
        }
        return e.prototype.addSegs = function(e) {
            for (var t = [], n = 0, r = e; n < r.length; n++) {
                var o = r[n];
                this.insertEntry(o, t)
            }
            return t
        }
        ,
        e.prototype.insertEntry = function(e, t) {
            var n = this.findInsertion(e);
            return this.isInsertionValid(n, e) ? (this.insertEntryAt(e, n),
            1) : this.handleInvalidInsertion(n, e, t)
        }
        ,
        e.prototype.isInsertionValid = function(e, t) {
            return (-1 === this.maxCoord || e.levelCoord + t.thickness <= this.maxCoord) && (-1 === this.maxStackCnt || e.stackCnt < this.maxStackCnt)
        }
        ,
        e.prototype.handleInvalidInsertion = function(e, t, n) {
            return this.allowReslicing && e.touchingEntry ? this.splitEntry(t, e.touchingEntry, n) : (n.push(t),
            0)
        }
        ,
        e.prototype.splitEntry = function(e, t, n) {
            var r = 0
              , i = []
              , a = e.span
              , s = t.span;
            return a.start < s.start && (r += this.insertEntry({
                index: e.index,
                thickness: e.thickness,
                span: {
                    start: a.start,
                    end: s.start
                }
            }, i)),
            a.end > s.end && (r += this.insertEntry({
                index: e.index,
                thickness: e.thickness,
                span: {
                    start: s.end,
                    end: a.end
                }
            }, i)),
            r ? (n.push.apply(n, o([{
                index: e.index,
                thickness: e.thickness,
                span: ya(s, a)
            }], i)),
            r) : (n.push(e),
            0)
        }
        ,
        e.prototype.insertEntryAt = function(e, t) {
            var n = this.entriesByLevel
              , r = this.levelCoords;
            -1 === t.lateral ? (Sa(r, t.level, t.levelCoord),
            Sa(n, t.level, [e])) : Sa(n[t.level], t.lateral, e),
            this.stackCnts[va(e)] = t.stackCnt
        }
        ,
        e.prototype.findInsertion = function(e) {
            for (var t = this, n = t.levelCoords, r = t.entriesByLevel, o = t.strictOrder, i = t.stackCnts, a = n.length, s = 0, l = -1, u = -1, c = null, d = 0, p = 0; p < a; p += 1) {
                var f = n[p];
                if (!o && f >= s + e.thickness)
                    break;
                for (var h = r[p], v = void 0, g = Ea(h, e.span.start, ha), m = g[0] + g[1]; (v = h[m]) && v.span.start < e.span.end; ) {
                    var y = f + v.thickness;
                    y > s && (s = y,
                    c = v,
                    l = p,
                    u = m),
                    y === s && (d = Math.max(d, i[va(v)] + 1)),
                    m += 1
                }
            }
            var S = 0;
            if (c)
                for (S = l + 1; S < a && n[S] < s; )
                    S += 1;
            var E = -1;
            return S < a && n[S] === s && (E = Ea(r[S], e.span.end, ha)[0]),
            {
                touchingLevel: l,
                touchingLateral: u,
                touchingEntry: c,
                stackCnt: d,
                levelCoord: s,
                level: S,
                lateral: E
            }
        }
        ,
        e.prototype.toRects = function() {
            for (var e = this.entriesByLevel, t = this.levelCoords, n = e.length, o = [], i = 0; i < n; i += 1)
                for (var a = e[i], s = t[i], l = 0, u = a; l < u.length; l++) {
                    var c = u[l];
                    o.push(r(r({}, c), {
                        levelCoord: s
                    }))
                }
            return o
        }
        ,
        e
    }();
    function ha(e) {
        return e.span.end
    }
    function va(e) {
        return e.index + ":" + e.span.start
    }
    function ga(e) {
        for (var t = [], n = 0, r = e; n < r.length; n++) {
            for (var o = r[n], i = [], a = {
                span: o.span,
                entries: [o]
            }, s = 0, l = t; s < l.length; s++) {
                var u = l[s];
                ya(u.span, a.span) ? a = {
                    entries: u.entries.concat(a.entries),
                    span: ma(u.span, a.span)
                } : i.push(u)
            }
            i.push(a),
            t = i
        }
        return t
    }
    function ma(e, t) {
        return {
            start: Math.min(e.start, t.start),
            end: Math.max(e.end, t.end)
        }
    }
    function ya(e, t) {
        var n = Math.max(e.start, t.start)
          , r = Math.min(e.end, t.end);
        return n < r ? {
            start: n,
            end: r
        } : null
    }
    function Sa(e, t, n) {
        e.splice(t, 0, n)
    }
    function Ea(e, t, n) {
        var r = 0
          , o = e.length;
        if (!o || t < n(e[r]))
            return [0, 0];
        if (t > n(e[o - 1]))
            return [o, 0];
        for (; r < o; ) {
            var i = Math.floor(r + (o - r) / 2)
              , a = n(e[i]);
            if (t < a)
                o = i;
            else {
                if (!(t > a))
                    return [i, 1];
                r = i + 1
            }
        }
        return [r, 0]
    }
    var ba = function() {
        function e(e) {
            this.component = e.component,
            this.isHitComboAllowed = e.isHitComboAllowed || null
        }
        return e.prototype.destroy = function() {}
        ,
        e
    }();
    function Ca(e, t) {
        return {
            component: e,
            el: t.el,
            useEventCenter: null == t.useEventCenter || t.useEventCenter,
            isHitComboAllowed: t.isHitComboAllowed || null
        }
    }
    function Da(e) {
        var t;
        return (t = {})[e.component.uid] = e,
        t
    }
    var Ra = {}
      , wa = function() {
        function e(e, t) {
            this.emitter = new Bo
        }
        return e.prototype.destroy = function() {}
        ,
        e.prototype.setMirrorIsVisible = function(e) {}
        ,
        e.prototype.setMirrorNeedsRevert = function(e) {}
        ,
        e.prototype.setAutoScrollEnabled = function(e) {}
        ,
        e
    }()
      , Ta = {}
      , _a = {
        startTime: qt,
        duration: qt,
        create: Boolean,
        sourceId: String
    };
    function xa(e) {
        var t = An(e, _a)
          , n = t.refined
          , r = t.extra;
        return {
            startTime: n.startTime || null,
            duration: n.duration || null,
            create: null == n.create || n.create,
            sourceId: n.sourceId,
            leftoverProps: r
        }
    }
    var ka = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props.widgetGroups.map((function(t) {
                return e.renderWidgetGroup(t)
            }
            ));
            return Yo.apply(void 0, o(["div", {
                className: "fc-toolbar-chunk"
            }], t))
        }
        ,
        t.prototype.renderWidgetGroup = function(e) {
            for (var t = this.props, n = this.context.theme, r = [], i = !0, a = 0, s = e; a < s.length; a++) {
                var l = s[a]
                  , u = l.buttonName
                  , c = l.buttonClick
                  , d = l.buttonText
                  , p = l.buttonIcon
                  , f = l.buttonHint;
                if ("title" === u)
                    i = !1,
                    r.push(Yo("h2", {
                        className: "fc-toolbar-title",
                        id: t.titleId
                    }, t.title));
                else {
                    var h = u === t.activeButton
                      , v = !t.isTodayEnabled && "today" === u || !t.isPrevEnabled && "prev" === u || !t.isNextEnabled && "next" === u
                      , g = ["fc-" + u + "-button", n.getClass("button")];
                    h && g.push(n.getClass("buttonActive")),
                    r.push(Yo("button", {
                        type: "button",
                        title: "function" == typeof f ? f(t.navUnit) : f,
                        disabled: v,
                        "aria-pressed": h,
                        className: g.join(" "),
                        onClick: c
                    }, d || (p ? Yo("span", {
                        className: p
                    }) : "")))
                }
            }
            if (r.length > 1) {
                var m = i && n.getClass("buttonGroup") || "";
                return Yo.apply(void 0, o(["div", {
                    className: m
                }], r))
            }
            return r[0]
        }
        ,
        t
    }(ii)
      , Ma = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e, t, n = this.props, r = n.model, o = n.extraClassName, i = !1, a = r.sectionWidgets, s = a.center;
            return a.left ? (i = !0,
            e = a.left) : e = a.start,
            a.right ? (i = !0,
            t = a.right) : t = a.end,
            Yo("div", {
                className: [o || "", "fc-toolbar", i ? "fc-toolbar-ltr" : ""].join(" ")
            }, this.renderSection("start", e || []), this.renderSection("center", s || []), this.renderSection("end", t || []))
        }
        ,
        t.prototype.renderSection = function(e, t) {
            var n = this.props;
            return Yo(ka, {
                key: e,
                widgetGroups: t,
                title: n.title,
                navUnit: n.navUnit,
                activeButton: n.activeButton,
                isTodayEnabled: n.isTodayEnabled,
                isPrevEnabled: n.isPrevEnabled,
                isNextEnabled: n.isNextEnabled,
                titleId: n.titleId
            })
        }
        ,
        t
    }(ii)
      , Ia = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.state = {
                availableWidth: null
            },
            t.handleEl = function(e) {
                t.el = e,
                li(t.props.elRef, e),
                t.updateAvailableWidth()
            }
            ,
            t.handleResize = function() {
                t.updateAvailableWidth()
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.state
              , n = e.aspectRatio
              , r = ["fc-view-harness", n || e.liquid || e.height ? "fc-view-harness-active" : "fc-view-harness-passive"]
              , o = ""
              , i = "";
            return n ? null !== t.availableWidth ? o = t.availableWidth / n : i = 1 / n * 100 + "%" : o = e.height || "",
            Yo("div", {
                "aria-labelledby": e.labeledById,
                ref: this.handleEl,
                className: r.join(" "),
                style: {
                    height: o,
                    paddingBottom: i
                }
            }, e.children)
        }
        ,
        t.prototype.componentDidMount = function() {
            this.context.addResizeHandler(this.handleResize)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.context.removeResizeHandler(this.handleResize)
        }
        ,
        t.prototype.updateAvailableWidth = function() {
            this.el && this.props.aspectRatio && this.setState({
                availableWidth: this.el.offsetWidth
            })
        }
        ,
        t
    }(ii)
      , Pa = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            return n.handleSegClick = function(e, t) {
                var r = n.component
                  , o = r.context
                  , i = mr(t);
                if (i && r.isValidSegDownEl(e.target)) {
                    var a = Pe(e.target, ".fc-event-forced-url")
                      , s = a ? a.querySelector("a[href]").href : "";
                    o.emitter.trigger("eventClick", {
                        el: t,
                        event: new Zr(r.context,i.eventRange.def,i.eventRange.instance),
                        jsEvent: e,
                        view: o.viewApi
                    }),
                    s && !e.defaultPrevented && (window.location.href = s)
                }
            }
            ,
            n.destroy = Ge(t.el, "click", ".fc-event", n.handleSegClick),
            n
        }
        return n(t, e),
        t
    }(ba)
      , Na = function(e) {
        function t(t) {
            var n, r, o, i, a, s = e.call(this, t) || this;
            return s.handleEventElRemove = function(e) {
                e === s.currentSegEl && s.handleSegLeave(null, s.currentSegEl)
            }
            ,
            s.handleSegEnter = function(e, t) {
                mr(t) && (s.currentSegEl = t,
                s.triggerEvent("eventMouseEnter", e, t))
            }
            ,
            s.handleSegLeave = function(e, t) {
                s.currentSegEl && (s.currentSegEl = null,
                s.triggerEvent("eventMouseLeave", e, t))
            }
            ,
            s.removeHoverListeners = (n = t.el,
            r = ".fc-event",
            o = s.handleSegEnter,
            i = s.handleSegLeave,
            Ge(n, "mouseover", r, (function(e, t) {
                if (t !== a) {
                    a = t,
                    o(e, t);
                    var n = function(e) {
                        a = null,
                        i(e, t),
                        t.removeEventListener("mouseleave", n)
                    };
                    t.addEventListener("mouseleave", n)
                }
            }
            ))),
            s
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.removeHoverListeners()
        }
        ,
        t.prototype.triggerEvent = function(e, t, n) {
            var r = this.component
              , o = r.context
              , i = mr(n);
            t && !r.isValidSegDownEl(t.target) || o.emitter.trigger(e, {
                el: n,
                event: new Zr(o,i.eventRange.def,i.eventRange.instance),
                jsEvent: t,
                view: o.viewApi
            })
        }
        ,
        t
    }(ba)
      , Ha = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildViewContext = cn(ri),
            t.buildViewPropTransformers = cn(Aa),
            t.buildToolbarProps = cn(Oa),
            t.headerRef = Xo(),
            t.footerRef = Xo(),
            t.interactionsStore = {},
            t.state = {
                viewLabelId: Ve()
            },
            t.registerInteractiveComponent = function(e, n) {
                var r = Ca(e, n)
                  , o = [Pa, Na].concat(t.props.pluginHooks.componentInteractions).map((function(e) {
                    return new e(r)
                }
                ));
                t.interactionsStore[e.uid] = o,
                Ra[e.uid] = r
            }
            ,
            t.unregisterInteractiveComponent = function(e) {
                var n = t.interactionsStore[e.uid];
                if (n) {
                    for (var r = 0, o = n; r < o.length; r++) {
                        o[r].destroy()
                    }
                    delete t.interactionsStore[e.uid]
                }
                delete Ra[e.uid]
            }
            ,
            t.resizeRunner = new $i((function() {
                t.props.emitter.trigger("_resize", !0),
                t.props.emitter.trigger("windowResize", {
                    view: t.props.viewApi
                })
            }
            )),
            t.handleWindowResize = function(e) {
                var n = t.props.options;
                n.handleWindowResize && e.target === window && t.resizeRunner.request(n.windowResizeDelay)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e, t = this.props, n = t.toolbarConfig, o = t.options, i = this.buildToolbarProps(t.viewSpec, t.dateProfile, t.dateProfileGenerator, t.currentDate, qr(t.options.now, t.dateEnv), t.viewTitle), a = !1, s = "";
            t.isHeightAuto || t.forPrint ? s = "" : null != o.height ? a = !0 : null != o.contentHeight ? s = o.contentHeight : e = Math.max(o.aspectRatio, .5);
            var l = this.buildViewContext(t.viewSpec, t.viewApi, t.options, t.dateProfileGenerator, t.dateEnv, t.theme, t.pluginHooks, t.dispatch, t.getCurrentData, t.emitter, t.calendarApi, this.registerInteractiveComponent, this.unregisterInteractiveComponent)
              , u = n.header && n.header.hasTitle ? this.state.viewLabelId : "";
            return Yo(ni.Provider, {
                value: l
            }, n.header && Yo(Ma, r({
                ref: this.headerRef,
                extraClassName: "fc-header-toolbar",
                model: n.header,
                titleId: u
            }, i)), Yo(Ia, {
                liquid: a,
                height: s,
                aspectRatio: e,
                labeledById: u
            }, this.renderView(t), this.buildAppendContent()), n.footer && Yo(Ma, r({
                ref: this.footerRef,
                extraClassName: "fc-footer-toolbar",
                model: n.footer,
                titleId: ""
            }, i)))
        }
        ,
        t.prototype.componentDidMount = function() {
            var e = this.props;
            this.calendarInteractions = e.pluginHooks.calendarInteractions.map((function(t) {
                return new t(e)
            }
            )),
            window.addEventListener("resize", this.handleWindowResize);
            var t = e.pluginHooks.propSetHandlers;
            for (var n in t)
                t[n](e[n], e)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            var t = this.props
              , n = t.pluginHooks.propSetHandlers;
            for (var r in n)
                t[r] !== e[r] && n[r](t[r], t)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            window.removeEventListener("resize", this.handleWindowResize),
            this.resizeRunner.clear();
            for (var e = 0, t = this.calendarInteractions; e < t.length; e++) {
                t[e].destroy()
            }
            this.props.emitter.trigger("_unmount")
        }
        ,
        t.prototype.buildAppendContent = function() {
            var e = this.props
              , t = e.pluginHooks.viewContainerAppends.map((function(t) {
                return t(e)
            }
            ));
            return Yo.apply(void 0, o([Ko, {}], t))
        }
        ,
        t.prototype.renderView = function(e) {
            for (var t = e.pluginHooks, n = e.viewSpec, o = {
                dateProfile: e.dateProfile,
                businessHours: e.businessHours,
                eventStore: e.renderableEventStore,
                eventUiBases: e.eventUiBases,
                dateSelection: e.dateSelection,
                eventSelection: e.eventSelection,
                eventDrag: e.eventDrag,
                eventResize: e.eventResize,
                isHeightAuto: e.isHeightAuto,
                forPrint: e.forPrint
            }, i = 0, a = this.buildViewPropTransformers(t.viewPropsTransformers); i < a.length; i++) {
                var s = a[i];
                r(o, s.transform(o, e))
            }
            var l = n.component;
            return Yo(l, r({}, o))
        }
        ,
        t
    }(oi);
    function Oa(e, t, n, r, o, i) {
        var a = n.build(o, void 0, !1)
          , s = n.buildPrev(t, r, !1)
          , l = n.buildNext(t, r, !1);
        return {
            title: i,
            activeButton: e.type,
            navUnit: e.singleUnit,
            isTodayEnabled: a.isValid && !fr(t.currentRange, o),
            isPrevEnabled: s.isValid,
            isNextEnabled: l.isValid
        }
    }
    function Aa(e) {
        return e.map((function(e) {
            return new e
        }
        ))
    }
    var Wa = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.state = {
                forPrint: !1
            },
            t.handleBeforePrint = function() {
                t.setState({
                    forPrint: !0
                })
            }
            ,
            t.handleAfterPrint = function() {
                t.setState({
                    forPrint: !1
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = e.options
              , n = this.state.forPrint
              , r = n || "auto" === t.height || "auto" === t.contentHeight
              , o = r || null == t.height ? "" : t.height
              , i = ["fc", n ? "fc-media-print" : "fc-media-screen", "fc-direction-" + t.direction, e.theme.getClass("root")];
            return Eo() || i.push("fc-liquid-hack"),
            e.children(i, o, r, n)
        }
        ,
        t.prototype.componentDidMount = function() {
            var e = this.props.emitter;
            e.on("_beforeprint", this.handleBeforePrint),
            e.on("_afterprint", this.handleAfterPrint)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            var e = this.props.emitter;
            e.off("_beforeprint", this.handleBeforePrint),
            e.off("_afterprint", this.handleAfterPrint)
        }
        ,
        t
    }(ii);
    function La(e, t) {
        return _n(!e || t > 10 ? {
            weekday: "short"
        } : t > 1 ? {
            weekday: "short",
            month: "numeric",
            day: "numeric",
            omitCommas: !0
        } : {
            weekday: "long"
        })
    }
    var Ua = "fc-col-header-cell";
    function Ba(e) {
        return e.text
    }
    var za = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.context
              , t = e.dateEnv
              , n = e.options
              , o = e.theme
              , i = e.viewApi
              , a = this.props
              , s = a.date
              , l = a.dateProfile
              , u = Ro(s, a.todayRange, null, l)
              , c = [Ua].concat(wo(u, o))
              , d = t.format(s, a.dayHeaderFormat)
              , p = !u.isDisabled && a.colCnt > 1 ? ko(this.context, s) : {}
              , f = r(r(r({
                date: t.toDate(s),
                view: i
            }, a.extraHookProps), {
                text: d
            }), u);
            return Yo(hi, {
                hookProps: f,
                classNames: n.dayHeaderClassNames,
                content: n.dayHeaderContent,
                defaultContent: Ba,
                didMount: n.dayHeaderDidMount,
                willUnmount: n.dayHeaderWillUnmount
            }, (function(e, t, n, o) {
                return Yo("th", r({
                    ref: e,
                    role: "columnheader",
                    className: c.concat(t).join(" "),
                    "data-date": u.isDisabled ? void 0 : on(s),
                    colSpan: a.colSpan
                }, a.extraDataAttrs), Yo("div", {
                    className: "fc-scrollgrid-sync-inner"
                }, !u.isDisabled && Yo("a", r({
                    ref: n,
                    className: ["fc-col-header-cell-cushion", a.isSticky ? "fc-sticky" : ""].join(" ")
                }, p), o)))
            }
            ))
        }
        ,
        t
    }(ii)
      , Va = _n({
        weekday: "long"
    })
      , Fa = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.dateEnv
              , o = t.theme
              , i = t.viewApi
              , a = t.options
              , s = ht(new Date(2592e5), e.dow)
              , l = {
                dow: e.dow,
                isDisabled: !1,
                isFuture: !1,
                isPast: !1,
                isToday: !1,
                isOther: !1
            }
              , u = [Ua].concat(wo(l, o), e.extraClassNames || [])
              , c = n.format(s, e.dayHeaderFormat)
              , d = r(r(r(r({
                date: s
            }, l), {
                view: i
            }), e.extraHookProps), {
                text: c
            });
            return Yo(hi, {
                hookProps: d,
                classNames: a.dayHeaderClassNames,
                content: a.dayHeaderContent,
                defaultContent: Ba,
                didMount: a.dayHeaderDidMount,
                willUnmount: a.dayHeaderWillUnmount
            }, (function(t, o, i, a) {
                return Yo("th", r({
                    ref: t,
                    role: "columnheader",
                    className: u.concat(o).join(" "),
                    colSpan: e.colSpan
                }, e.extraDataAttrs), Yo("div", {
                    className: "fc-scrollgrid-sync-inner"
                }, Yo("a", {
                    "aria-label": n.format(s, Va),
                    className: ["fc-col-header-cell-cushion", e.isSticky ? "fc-sticky" : ""].join(" "),
                    ref: i
                }, a)))
            }
            ))
        }
        ,
        t
    }(ii)
      , Ga = function(e) {
        function t(t, n) {
            var r = e.call(this, t, n) || this;
            return r.initialNowDate = qr(n.options.now, n.dateEnv),
            r.initialNowQueriedMs = (new Date).valueOf(),
            r.state = r.computeTiming().currentState,
            r
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.state;
            return e.children(t.nowDate, t.todayRange)
        }
        ,
        t.prototype.componentDidMount = function() {
            this.setTimeout()
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            e.unit !== this.props.unit && (this.clearTimeout(),
            this.setTimeout())
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.clearTimeout()
        }
        ,
        t.prototype.computeTiming = function() {
            var e = this.props
              , t = this.context
              , n = vt(this.initialNowDate, (new Date).valueOf() - this.initialNowQueriedMs)
              , r = t.dateEnv.startOf(n, e.unit)
              , o = t.dateEnv.add(r, qt(1, e.unit))
              , i = o.valueOf() - n.valueOf();
            return i = Math.min(864e5, i),
            {
                currentState: {
                    nowDate: r,
                    todayRange: ja(r)
                },
                nextState: {
                    nowDate: o,
                    todayRange: ja(o)
                },
                waitMs: i
            }
        }
        ,
        t.prototype.setTimeout = function() {
            var e = this
              , t = this.computeTiming()
              , n = t.nextState
              , r = t.waitMs;
            this.timeoutId = setTimeout((function() {
                e.setState(n, (function() {
                    e.setTimeout()
                }
                ))
            }
            ), r)
        }
        ,
        t.prototype.clearTimeout = function() {
            this.timeoutId && clearTimeout(this.timeoutId)
        }
        ,
        t.contextType = ni,
        t
    }(qo);
    function ja(e) {
        var t = bt(e);
        return {
            start: t,
            end: ht(t, 1)
        }
    }
    var qa = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.createDayHeaderFormatter = cn(Ya),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.context
              , t = this.props
              , n = t.dates
              , r = t.dateProfile
              , o = t.datesRepDistinctDays
              , i = t.renderIntro
              , a = this.createDayHeaderFormatter(e.options.dayHeaderFormat, o, n.length);
            return Yo(Ga, {
                unit: "day"
            }, (function(e, t) {
                return Yo("tr", {
                    role: "row"
                }, i && i("day"), n.map((function(e) {
                    return o ? Yo(za, {
                        key: e.toISOString(),
                        date: e,
                        dateProfile: r,
                        todayRange: t,
                        colCnt: n.length,
                        dayHeaderFormat: a
                    }) : Yo(Fa, {
                        key: e.getUTCDay(),
                        dow: e.getUTCDay(),
                        dayHeaderFormat: a
                    })
                }
                )))
            }
            ))
        }
        ,
        t
    }(ii);
    function Ya(e, t, n) {
        return e || La(t, n)
    }
    var Za = function() {
        function e(e, t) {
            for (var n = e.start, r = e.end, o = [], i = [], a = -1; n < r; )
                t.isHiddenDay(n) ? o.push(a + .5) : (a += 1,
                o.push(a),
                i.push(n)),
                n = ht(n, 1);
            this.dates = i,
            this.indices = o,
            this.cnt = i.length
        }
        return e.prototype.sliceRange = function(e) {
            var t = this.getDateDayIndex(e.start)
              , n = this.getDateDayIndex(ht(e.end, -1))
              , r = Math.max(0, t)
              , o = Math.min(this.cnt - 1, n);
            return (r = Math.ceil(r)) <= (o = Math.floor(o)) ? {
                firstIndex: r,
                lastIndex: o,
                isStart: t === r,
                isEnd: n === o
            } : null
        }
        ,
        e.prototype.getDateDayIndex = function(e) {
            var t = this.indices
              , n = Math.floor(mt(this.dates[0], e));
            return n < 0 ? t[0] - 1 : n >= t.length ? t[t.length - 1] + 1 : t[n]
        }
        ,
        e
    }()
      , Xa = function() {
        function e(e, t) {
            var n, r, o, i = e.dates;
            if (t) {
                for (r = i[0].getUTCDay(),
                n = 1; n < i.length && i[n].getUTCDay() !== r; n += 1)
                    ;
                o = Math.ceil(i.length / n)
            } else
                o = 1,
                n = i.length;
            this.rowCnt = o,
            this.colCnt = n,
            this.daySeries = e,
            this.cells = this.buildCells(),
            this.headerDates = this.buildHeaderDates()
        }
        return e.prototype.buildCells = function() {
            for (var e = [], t = 0; t < this.rowCnt; t += 1) {
                for (var n = [], r = 0; r < this.colCnt; r += 1)
                    n.push(this.buildCell(t, r));
                e.push(n)
            }
            return e
        }
        ,
        e.prototype.buildCell = function(e, t) {
            var n = this.daySeries.dates[e * this.colCnt + t];
            return {
                key: n.toISOString(),
                date: n
            }
        }
        ,
        e.prototype.buildHeaderDates = function() {
            for (var e = [], t = 0; t < this.colCnt; t += 1)
                e.push(this.cells[0][t].date);
            return e
        }
        ,
        e.prototype.sliceRange = function(e) {
            var t = this.colCnt
              , n = this.daySeries.sliceRange(e)
              , r = [];
            if (n)
                for (var o = n.firstIndex, i = n.lastIndex, a = o; a <= i; ) {
                    var s = Math.floor(a / t)
                      , l = Math.min((s + 1) * t, i + 1);
                    r.push({
                        row: s,
                        firstCol: a % t,
                        lastCol: (l - 1) % t,
                        isStart: n.isStart && a === o,
                        isEnd: n.isEnd && l - 1 === i
                    }),
                    a = l
                }
            return r
        }
        ,
        e
    }()
      , Ka = function() {
        function e() {
            this.sliceBusinessHours = cn(this._sliceBusinessHours),
            this.sliceDateSelection = cn(this._sliceDateSpan),
            this.sliceEventStore = cn(this._sliceEventStore),
            this.sliceEventDrag = cn(this._sliceInteraction),
            this.sliceEventResize = cn(this._sliceInteraction),
            this.forceDayIfListItem = !1
        }
        return e.prototype.sliceProps = function(e, t, n, r) {
            for (var i = [], a = 4; a < arguments.length; a++)
                i[a - 4] = arguments[a];
            var s = e.eventUiBases
              , l = this.sliceEventStore.apply(this, o([e.eventStore, s, t, n], i));
            return {
                dateSelectionSegs: this.sliceDateSelection.apply(this, o([e.dateSelection, s, r], i)),
                businessHourSegs: this.sliceBusinessHours.apply(this, o([e.businessHours, t, n, r], i)),
                fgEventSegs: l.fg,
                bgEventSegs: l.bg,
                eventDrag: this.sliceEventDrag.apply(this, o([e.eventDrag, s, t, n], i)),
                eventResize: this.sliceEventResize.apply(this, o([e.eventResize, s, t, n], i)),
                eventSelection: e.eventSelection
            }
        }
        ,
        e.prototype.sliceNowDate = function(e, t) {
            for (var n = [], r = 2; r < arguments.length; r++)
                n[r - 2] = arguments[r];
            return this._sliceDateSpan.apply(this, o([{
                range: {
                    start: e,
                    end: vt(e, 1)
                },
                allDay: !1
            }, {}, t], n))
        }
        ,
        e.prototype._sliceBusinessHours = function(e, t, n, r) {
            for (var i = [], a = 4; a < arguments.length; a++)
                i[a - 4] = arguments[a];
            return e ? this._sliceEventStore.apply(this, o([Vt(e, $a(t, Boolean(n)), r), {}, t, n], i)).bg : []
        }
        ,
        e.prototype._sliceEventStore = function(e, t, n, r) {
            for (var o = [], i = 4; i < arguments.length; i++)
                o[i - 4] = arguments[i];
            if (e) {
                var a = hr(e, t, $a(n, Boolean(r)), r);
                return {
                    bg: this.sliceEventRanges(a.bg, o),
                    fg: this.sliceEventRanges(a.fg, o)
                }
            }
            return {
                bg: [],
                fg: []
            }
        }
        ,
        e.prototype._sliceInteraction = function(e, t, n, r) {
            for (var o = [], i = 4; i < arguments.length; i++)
                o[i - 4] = arguments[i];
            if (!e)
                return null;
            var a = hr(e.mutatedEvents, t, $a(n, Boolean(r)), r);
            return {
                segs: this.sliceEventRanges(a.fg, o),
                affectedInstances: e.affectedEvents.instances,
                isEvent: e.isEvent
            }
        }
        ,
        e.prototype._sliceDateSpan = function(e, t, n) {
            for (var r = [], i = 3; i < arguments.length; i++)
                r[i - 3] = arguments[i];
            if (!e)
                return [];
            for (var a = Or(e, t, n), s = this.sliceRange.apply(this, o([e.range], r)), l = 0, u = s; l < u.length; l++) {
                var c = u[l];
                c.eventRange = a
            }
            return s
        }
        ,
        e.prototype.sliceEventRanges = function(e, t) {
            for (var n = [], r = 0, o = e; r < o.length; r++) {
                var i = o[r];
                n.push.apply(n, this.sliceEventRange(i, t))
            }
            return n
        }
        ,
        e.prototype.sliceEventRange = function(e, t) {
            var n = e.range;
            this.forceDayIfListItem && "list-item" === e.ui.display && (n = {
                start: n.start,
                end: ht(n.start, 1)
            });
            for (var r = this.sliceRange.apply(this, o([n], t)), i = 0, a = r; i < a.length; i++) {
                var s = a[i];
                s.eventRange = e,
                s.isStart = e.isStart && s.isStart,
                s.isEnd = e.isEnd && s.isEnd
            }
            return r
        }
        ,
        e
    }();
    function $a(e, t) {
        var n = e.activeRange;
        return t ? n : {
            start: vt(n.start, e.slotMinTime.milliseconds),
            end: vt(n.end, e.slotMaxTime.milliseconds - 864e5)
        }
    }
    function Ja(e, t, n) {
        var r = e.mutatedEvents.instances;
        for (var o in r)
            if (!pr(t.validRange, r[o].range))
                return !1;
        return es({
            eventDrag: e
        }, n)
    }
    function Qa(e, t, n) {
        return !!pr(t.validRange, e.range) && es({
            dateSelection: e
        }, n)
    }
    function es(e, t) {
        var n = t.getCurrentData()
          , o = r({
            businessHours: n.businessHours,
            dateSelection: "",
            eventStore: n.eventStore,
            eventUiBases: n.eventUiBases,
            eventSelection: "",
            eventDrag: null,
            eventResize: null
        }, e);
        return (t.pluginHooks.isPropsValid || ts)(o, t)
    }
    function ts(e, t, n, o) {
        return void 0 === n && (n = {}),
        !(e.eventDrag && !function(e, t, n, o) {
            var i = t.getCurrentData()
              , a = e.eventDrag
              , s = a.mutatedEvents
              , l = s.defs
              , u = s.instances
              , c = yr(l, a.isEvent ? e.eventUiBases : {
                "": i.selectionConfig
            });
            o && (c = Ht(c, o));
            var d = (v = e.eventStore,
            g = a.affectedEvents.instances,
            {
                defs: v.defs,
                instances: Nt(v.instances, (function(e) {
                    return !g[e.instanceId]
                }
                ))
            })
              , p = d.defs
              , f = d.instances
              , h = yr(p, e.eventUiBases);
            var v, g;
            for (var m in u) {
                var y = u[m]
                  , S = y.range
                  , E = c[y.defId]
                  , b = l[y.defId];
                if (!ns(E.constraints, S, d, e.businessHours, t))
                    return !1;
                var C = t.options.eventOverlap
                  , D = "function" == typeof C ? C : null;
                for (var R in f) {
                    var w = f[R];
                    if (dr(S, w.range)) {
                        if (!1 === h[w.defId].overlap && a.isEvent)
                            return !1;
                        if (!1 === E.overlap)
                            return !1;
                        if (D && !D(new Zr(t,p[w.defId],w), new Zr(t,b,y)))
                            return !1
                    }
                }
                for (var T = i.eventStore, _ = 0, x = E.allows; _ < x.length; _++) {
                    var k = x[_]
                      , M = r(r({}, n), {
                        range: y.range,
                        allDay: b.allDay
                    })
                      , I = T.defs[b.defId]
                      , P = T.instances[m]
                      , N = void 0;
                    if (N = I ? new Zr(t,I,P) : new Zr(t,b),
                    !k(Wr(M, t), N))
                        return !1
                }
            }
            return !0
        }(e, t, n, o)) && !(e.dateSelection && !function(e, t, n, o) {
            var i = e.eventStore
              , a = i.defs
              , s = i.instances
              , l = e.dateSelection
              , u = l.range
              , c = t.getCurrentData().selectionConfig;
            o && (c = o(c));
            if (!ns(c.constraints, u, i, e.businessHours, t))
                return !1;
            var d = t.options.selectOverlap
              , p = "function" == typeof d ? d : null;
            for (var f in s) {
                var h = s[f];
                if (dr(u, h.range)) {
                    if (!1 === c.overlap)
                        return !1;
                    if (p && !p(new Zr(t,a[h.defId],h), null))
                        return !1
                }
            }
            for (var v = 0, g = c.allows; v < g.length; v++) {
                if (!(0,
                g[v])(Wr(r(r({}, n), l), t), null))
                    return !1
            }
            return !0
        }(e, t, n, o))
    }
    function ns(e, t, n, r, o) {
        for (var i = 0, a = e; i < a.length; i++) {
            if (!is(rs(a[i], t, n, r, o), t))
                return !1
        }
        return !0
    }
    function rs(e, t, n, r, o) {
        return "businessHours" === e ? os(Vt(r, t, o)) : "string" == typeof e ? os(Fn(n, (function(t) {
            return t.groupId === e
        }
        ))) : "object" == typeof e && e ? os(Vt(e, t, o)) : []
    }
    function os(e) {
        var t = e.instances
          , n = [];
        for (var r in t)
            n.push(t[r].range);
        return n
    }
    function is(e, t) {
        for (var n = 0, r = e; n < r.length; n++) {
            if (pr(r[n], t))
                return !0
        }
        return !1
    }
    var as = /^(visible|hidden)$/
      , ss = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.handleEl = function(e) {
                t.el = e,
                li(t.props.elRef, e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = e.liquid
              , n = e.liquidIsAbsolute
              , r = t && n
              , o = ["fc-scroller"];
            return t && (n ? o.push("fc-scroller-liquid-absolute") : o.push("fc-scroller-liquid")),
            Yo("div", {
                ref: this.handleEl,
                className: o.join(" "),
                style: {
                    overflowX: e.overflowX,
                    overflowY: e.overflowY,
                    left: r && -(e.overcomeLeft || 0) || "",
                    right: r && -(e.overcomeRight || 0) || "",
                    bottom: r && -(e.overcomeBottom || 0) || "",
                    marginLeft: !r && -(e.overcomeLeft || 0) || "",
                    marginRight: !r && -(e.overcomeRight || 0) || "",
                    marginBottom: !r && -(e.overcomeBottom || 0) || "",
                    maxHeight: e.maxHeight || ""
                }
            }, e.children)
        }
        ,
        t.prototype.needsXScrolling = function() {
            if (as.test(this.props.overflowX))
                return !1;
            for (var e = this.el, t = this.el.getBoundingClientRect().width - this.getYScrollbarWidth(), n = e.children, r = 0; r < n.length; r += 1) {
                if (n[r].getBoundingClientRect().width > t)
                    return !0
            }
            return !1
        }
        ,
        t.prototype.needsYScrolling = function() {
            if (as.test(this.props.overflowY))
                return !1;
            for (var e = this.el, t = this.el.getBoundingClientRect().height - this.getXScrollbarWidth(), n = e.children, r = 0; r < n.length; r += 1) {
                if (n[r].getBoundingClientRect().height > t)
                    return !0
            }
            return !1
        }
        ,
        t.prototype.getXScrollbarWidth = function() {
            return as.test(this.props.overflowX) ? 0 : this.el.offsetHeight - this.el.clientHeight
        }
        ,
        t.prototype.getYScrollbarWidth = function() {
            return as.test(this.props.overflowY) ? 0 : this.el.offsetWidth - this.el.clientWidth
        }
        ,
        t
    }(ii)
      , ls = function() {
        function e(e) {
            var t = this;
            this.masterCallback = e,
            this.currentMap = {},
            this.depths = {},
            this.callbackMap = {},
            this.handleValue = function(e, n) {
                var r = t
                  , o = r.depths
                  , i = r.currentMap
                  , a = !1
                  , s = !1;
                null !== e ? (a = n in i,
                i[n] = e,
                o[n] = (o[n] || 0) + 1,
                s = !0) : (o[n] -= 1,
                o[n] || (delete i[n],
                delete t.callbackMap[n],
                a = !0)),
                t.masterCallback && (a && t.masterCallback(null, String(n)),
                s && t.masterCallback(e, String(n)))
            }
        }
        return e.prototype.createRef = function(e) {
            var t = this
              , n = this.callbackMap[e];
            return n || (n = this.callbackMap[e] = function(n) {
                t.handleValue(n, String(e))
            }
            ),
            n
        }
        ,
        e.prototype.collect = function(e, t, n) {
            return zt(this.currentMap, e, t, n)
        }
        ,
        e.prototype.getAll = function() {
            return At(this.currentMap)
        }
        ,
        e
    }();
    function us(e) {
        for (var t = 0, n = 0, r = He(e, ".fc-scrollgrid-shrink"); n < r.length; n++) {
            var o = r[n];
            t = Math.max(t, dt(o))
        }
        return Math.ceil(t)
    }
    function cs(e, t) {
        return e.liquid && t.liquid
    }
    function ds(e, t) {
        return null != t.maxHeight || cs(e, t)
    }
    function ps(e, t, n, r) {
        var o = n.expandRows;
        return "function" == typeof t.content ? t.content(n) : Yo("table", {
            role: "presentation",
            className: [t.tableClassName, e.syncRowHeights ? "fc-scrollgrid-sync-table" : ""].join(" "),
            style: {
                minWidth: n.tableMinWidth,
                width: n.clientWidth,
                height: o ? n.clientHeight : ""
            }
        }, n.tableColGroupNode, Yo(r ? "thead" : "tbody", {
            role: "presentation"
        }, "function" == typeof t.rowContent ? t.rowContent(n) : t.rowContent))
    }
    function fs(e, t) {
        return un(e, t, Wt)
    }
    function hs(e, t) {
        for (var n = [], r = 0, i = e; r < i.length; r++)
            for (var a = i[r], s = a.span || 1, l = 0; l < s; l += 1)
                n.push(Yo("col", {
                    style: {
                        width: "shrink" === a.width ? vs(t) : a.width || "",
                        minWidth: a.minWidth || ""
                    }
                }));
        return Yo.apply(void 0, o(["colgroup", {}], n))
    }
    function vs(e) {
        return null == e ? 4 : e
    }
    function gs(e) {
        for (var t = 0, n = e; t < n.length; t++) {
            if ("shrink" === n[t].width)
                return !0
        }
        return !1
    }
    function ms(e, t) {
        var n = ["fc-scrollgrid", t.theme.getClass("table")];
        return e && n.push("fc-scrollgrid-liquid"),
        n
    }
    function ys(e, t) {
        var n = ["fc-scrollgrid-section", "fc-scrollgrid-section-" + e.type, e.className];
        return t && e.liquid && null == e.maxHeight && n.push("fc-scrollgrid-section-liquid"),
        e.isSticky && n.push("fc-scrollgrid-section-sticky"),
        n
    }
    function Ss(e) {
        return Yo("div", {
            className: "fc-scrollgrid-sticky-shim",
            style: {
                width: e.clientWidth,
                minWidth: e.tableMinWidth
            }
        })
    }
    function Es(e) {
        var t = e.stickyHeaderDates;
        return null != t && "auto" !== t || (t = "auto" === e.height || "auto" === e.viewHeight),
        t
    }
    function bs(e) {
        var t = e.stickyFooterScrollbar;
        return null != t && "auto" !== t || (t = "auto" === e.height || "auto" === e.viewHeight),
        t
    }
    var Cs = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.processCols = cn((function(e) {
                return e
            }
            ), fs),
            t.renderMicroColGroup = cn(hs),
            t.scrollerRefs = new ls,
            t.scrollerElRefs = new ls(t._handleScrollerEl.bind(t)),
            t.state = {
                shrinkWidth: null,
                forceYScrollbars: !1,
                scrollerClientWidths: {},
                scrollerClientHeights: {}
            },
            t.handleSizing = function() {
                t.safeSetState(r({
                    shrinkWidth: t.computeShrinkWidth()
                }, t.computeScrollerDims()))
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.props
              , n = e.state
              , r = e.context
              , i = t.sections || []
              , a = this.processCols(t.cols)
              , s = this.renderMicroColGroup(a, n.shrinkWidth)
              , l = ms(t.liquid, r);
            t.collapsibleWidth && l.push("fc-scrollgrid-collapsible");
            for (var u, c = i.length, d = 0, p = [], f = [], h = []; d < c && "header" === (u = i[d]).type; )
                p.push(this.renderSection(u, s, !0)),
                d += 1;
            for (; d < c && "body" === (u = i[d]).type; )
                f.push(this.renderSection(u, s, !1)),
                d += 1;
            for (; d < c && "footer" === (u = i[d]).type; )
                h.push(this.renderSection(u, s, !0)),
                d += 1;
            var v = !Eo()
              , g = {
                role: "rowgroup"
            };
            return Yo("table", {
                role: "grid",
                className: l.join(" "),
                style: {
                    height: t.height
                }
            }, Boolean(!v && p.length) && Yo.apply(void 0, o(["thead", g], p)), Boolean(!v && f.length) && Yo.apply(void 0, o(["tbody", g], f)), Boolean(!v && h.length) && Yo.apply(void 0, o(["tfoot", g], h)), v && Yo.apply(void 0, o(o(o(["tbody", g], p), f), h)))
        }
        ,
        t.prototype.renderSection = function(e, t, n) {
            return "outerContent"in e ? Yo(Ko, {
                key: e.key
            }, e.outerContent) : Yo("tr", {
                key: e.key,
                role: "presentation",
                className: ys(e, this.props.liquid).join(" ")
            }, this.renderChunkTd(e, t, e.chunk, n))
        }
        ,
        t.prototype.renderChunkTd = function(e, t, n, r) {
            if ("outerContent"in n)
                return n.outerContent;
            var o = this.props
              , i = this.state
              , a = i.forceYScrollbars
              , s = i.scrollerClientWidths
              , l = i.scrollerClientHeights
              , u = ds(o, e)
              , c = cs(o, e)
              , d = o.liquid ? a ? "scroll" : u ? "auto" : "hidden" : "visible"
              , p = e.key
              , f = ps(e, n, {
                tableColGroupNode: t,
                tableMinWidth: "",
                clientWidth: o.collapsibleWidth || void 0 === s[p] ? null : s[p],
                clientHeight: void 0 !== l[p] ? l[p] : null,
                expandRows: e.expandRows,
                syncRowHeights: !1,
                rowSyncHeights: [],
                reportRowHeightChange: function() {}
            }, r);
            return Yo(r ? "th" : "td", {
                ref: n.elRef,
                role: "presentation"
            }, Yo("div", {
                className: "fc-scroller-harness" + (c ? " fc-scroller-harness-liquid" : "")
            }, Yo(ss, {
                ref: this.scrollerRefs.createRef(p),
                elRef: this.scrollerElRefs.createRef(p),
                overflowY: d,
                overflowX: o.liquid ? "hidden" : "visible",
                maxHeight: e.maxHeight,
                liquid: c,
                liquidIsAbsolute: !0
            }, f)))
        }
        ,
        t.prototype._handleScrollerEl = function(e, t) {
            var n = function(e, t) {
                for (var n = 0, r = e; n < r.length; n++) {
                    var o = r[n];
                    if (o.key === t)
                        return o
                }
                return null
            }(this.props.sections, t);
            n && li(n.chunk.scrollerElRef, e)
        }
        ,
        t.prototype.componentDidMount = function() {
            this.handleSizing(),
            this.context.addResizeHandler(this.handleSizing)
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.handleSizing()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.context.removeResizeHandler(this.handleSizing)
        }
        ,
        t.prototype.computeShrinkWidth = function() {
            return gs(this.props.cols) ? us(this.scrollerElRefs.getAll()) : 0
        }
        ,
        t.prototype.computeScrollerDims = function() {
            var e = No()
              , t = this.scrollerRefs
              , n = this.scrollerElRefs
              , r = !1
              , o = {}
              , i = {};
            for (var a in t.currentMap) {
                var s = t.currentMap[a];
                if (s && s.needsYScrolling()) {
                    r = !0;
                    break
                }
            }
            for (var l = 0, u = this.props.sections; l < u.length; l++) {
                a = u[l].key;
                var c = n.currentMap[a];
                if (c) {
                    var d = c.parentNode;
                    o[a] = Math.floor(d.getBoundingClientRect().width - (r ? e.y : 0)),
                    i[a] = Math.floor(d.getBoundingClientRect().height)
                }
            }
            return {
                forceYScrollbars: r,
                scrollerClientWidths: o,
                scrollerClientHeights: i
            }
        }
        ,
        t
    }(ii);
    Cs.addStateEquality({
        scrollerClientWidths: Wt,
        scrollerClientHeights: Wt
    });
    var Ds = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.elRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options
              , r = e.seg
              , o = r.eventRange
              , i = o.ui
              , a = {
                event: new Zr(t,o.def,o.instance),
                view: t.viewApi,
                timeText: e.timeText,
                textColor: i.textColor,
                backgroundColor: i.backgroundColor,
                borderColor: i.borderColor,
                isDraggable: !e.disableDragging && Cr(r, t),
                isStartResizable: !e.disableResizing && Dr(r, t),
                isEndResizable: !e.disableResizing && Rr(r),
                isMirror: Boolean(e.isDragging || e.isResizing || e.isDateSelecting),
                isStart: Boolean(r.isStart),
                isEnd: Boolean(r.isEnd),
                isPast: Boolean(e.isPast),
                isFuture: Boolean(e.isFuture),
                isToday: Boolean(e.isToday),
                isSelected: Boolean(e.isSelected),
                isDragging: Boolean(e.isDragging),
                isResizing: Boolean(e.isResizing)
            }
              , s = _r(a).concat(i.classNames);
            return Yo(hi, {
                hookProps: a,
                classNames: n.eventClassNames,
                content: n.eventContent,
                defaultContent: e.defaultContent,
                didMount: n.eventDidMount,
                willUnmount: n.eventWillUnmount,
                elRef: this.elRef
            }, (function(t, n, r, o) {
                return e.children(t, s.concat(n), r, o, a)
            }
            ))
        }
        ,
        t.prototype.componentDidMount = function() {
            gr(this.elRef.current, this.props.seg)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            var t = this.props.seg;
            t !== e.seg && gr(this.elRef.current, t)
        }
        ,
        t
    }(ii)
      , Rs = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = e.seg
              , o = t.options.eventTimeFormat || e.defaultTimeFormat
              , i = wr(n, o, t, e.defaultDisplayEventTime, e.defaultDisplayEventEnd);
            return Yo(Ds, {
                seg: n,
                timeText: i,
                disableDragging: e.disableDragging,
                disableResizing: e.disableResizing,
                defaultContent: e.defaultContent || ws,
                isDragging: e.isDragging,
                isResizing: e.isResizing,
                isDateSelecting: e.isDateSelecting,
                isSelected: e.isSelected,
                isPast: e.isPast,
                isFuture: e.isFuture,
                isToday: e.isToday
            }, (function(o, i, a, s, l) {
                return Yo("a", r({
                    className: e.extraClassNames.concat(i).join(" "),
                    style: {
                        borderColor: l.borderColor,
                        backgroundColor: l.backgroundColor
                    },
                    ref: o
                }, kr(n, t)), Yo("div", {
                    className: "fc-event-main",
                    ref: a,
                    style: {
                        color: l.textColor
                    }
                }, s), l.isStartResizable && Yo("div", {
                    className: "fc-event-resizer fc-event-resizer-start"
                }), l.isEndResizable && Yo("div", {
                    className: "fc-event-resizer fc-event-resizer-end"
                }))
            }
            ))
        }
        ,
        t
    }(ii);
    function ws(e) {
        return Yo("div", {
            className: "fc-event-main-frame"
        }, e.timeText && Yo("div", {
            className: "fc-event-time"
        }, e.timeText), Yo("div", {
            className: "fc-event-title-container"
        }, Yo("div", {
            className: "fc-event-title fc-sticky"
        }, e.event.title || Yo(Ko, null, " "))))
    }
    var Ts = function(e) {
        return Yo(ni.Consumer, null, (function(t) {
            var n = t.options
              , r = {
                isAxis: e.isAxis,
                date: t.dateEnv.toDate(e.date),
                view: t.viewApi
            };
            return Yo(hi, {
                hookProps: r,
                classNames: n.nowIndicatorClassNames,
                content: n.nowIndicatorContent,
                didMount: n.nowIndicatorDidMount,
                willUnmount: n.nowIndicatorWillUnmount
            }, e.children)
        }
        ))
    }
      , _s = _n({
        day: "numeric"
    })
      , xs = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options
              , r = ks({
                date: e.date,
                dateProfile: e.dateProfile,
                todayRange: e.todayRange,
                showDayNumber: e.showDayNumber,
                extraProps: e.extraHookProps,
                viewApi: t.viewApi,
                dateEnv: t.dateEnv
            });
            return Yo(gi, {
                hookProps: r,
                content: n.dayCellContent,
                defaultContent: e.defaultContent
            }, e.children)
        }
        ,
        t
    }(ii);
    function ks(e) {
        var t = e.date
          , n = e.dateEnv
          , o = Ro(t, e.todayRange, null, e.dateProfile);
        return r(r(r({
            date: n.toDate(t),
            view: e.viewApi
        }, o), {
            dayNumberText: e.showDayNumber ? n.format(t, _s) : ""
        }), e.extraProps)
    }
    var Ms = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.refineHookProps = dn(ks),
            t.normalizeClassNames = Si(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options
              , r = this.refineHookProps({
                date: e.date,
                dateProfile: e.dateProfile,
                todayRange: e.todayRange,
                showDayNumber: e.showDayNumber,
                extraProps: e.extraHookProps,
                viewApi: t.viewApi,
                dateEnv: t.dateEnv
            })
              , o = wo(r, t.theme).concat(r.isDisabled ? [] : this.normalizeClassNames(n.dayCellClassNames, r))
              , i = r.isDisabled ? {} : {
                "data-date": on(e.date)
            };
            return Yo(yi, {
                hookProps: r,
                didMount: n.dayCellDidMount,
                willUnmount: n.dayCellWillUnmount,
                elRef: e.elRef
            }, (function(t) {
                return e.children(t, o, i, r.isDisabled)
            }
            ))
        }
        ,
        t
    }(ii);
    function Is(e) {
        return Yo("div", {
            className: "fc-" + e
        })
    }
    var Ps = function(e) {
        return Yo(Ds, {
            defaultContent: Ns,
            seg: e.seg,
            timeText: "",
            disableDragging: !0,
            disableResizing: !0,
            isDragging: !1,
            isResizing: !1,
            isDateSelecting: !1,
            isSelected: !1,
            isPast: e.isPast,
            isFuture: e.isFuture,
            isToday: e.isToday
        }, (function(e, t, n, r, o) {
            return Yo("div", {
                ref: e,
                className: ["fc-bg-event"].concat(t).join(" "),
                style: {
                    backgroundColor: o.backgroundColor
                }
            }, r)
        }
        ))
    };
    function Ns(e) {
        return e.event.title && Yo("div", {
            className: "fc-event-title"
        }, e.event.title)
    }
    var Hs = function(e) {
        return Yo(ni.Consumer, null, (function(t) {
            var n = t.dateEnv
              , r = t.options
              , o = e.date
              , i = r.weekNumberFormat || e.defaultFormat
              , a = n.computeWeekNumber(o)
              , s = n.format(o, i);
            return Yo(hi, {
                hookProps: {
                    num: a,
                    text: s,
                    date: o
                },
                classNames: r.weekNumberClassNames,
                content: r.weekNumberContent,
                defaultContent: Os,
                didMount: r.weekNumberDidMount,
                willUnmount: r.weekNumberWillUnmount
            }, e.children)
        }
        ))
    };
    function Os(e) {
        return e.text
    }
    var As = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.state = {
                titleId: Ve()
            },
            t.handleRootEl = function(e) {
                t.rootEl = e,
                t.props.elRef && li(t.props.elRef, e)
            }
            ,
            t.handleDocumentMouseDown = function(e) {
                var n = Ue(e);
                t.rootEl.contains(n) || t.handleCloseClick()
            }
            ,
            t.handleDocumentKeyDown = function(e) {
                "Escape" === e.key && t.handleCloseClick()
            }
            ,
            t.handleCloseClick = function() {
                var e = t.props.onClose;
                e && e()
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.context
              , t = e.theme
              , n = e.options
              , o = this.props
              , i = this.state
              , a = ["fc-popover", t.getClass("popover")].concat(o.extraClassNames || []);
            return Jo(Yo("div", r({
                id: o.id,
                className: a.join(" "),
                "aria-labelledby": i.titleId
            }, o.extraAttrs, {
                ref: this.handleRootEl
            }), Yo("div", {
                className: "fc-popover-header " + t.getClass("popoverHeader")
            }, Yo("span", {
                className: "fc-popover-title",
                id: i.titleId
            }, o.title), Yo("span", {
                className: "fc-popover-close " + t.getIconClass("close"),
                title: n.closeHint,
                onClick: this.handleCloseClick
            })), Yo("div", {
                className: "fc-popover-body " + t.getClass("popoverContent")
            }, o.children)), o.parentEl)
        }
        ,
        t.prototype.componentDidMount = function() {
            document.addEventListener("mousedown", this.handleDocumentMouseDown),
            document.addEventListener("keydown", this.handleDocumentKeyDown),
            this.updateSize()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            document.removeEventListener("mousedown", this.handleDocumentMouseDown),
            document.removeEventListener("keydown", this.handleDocumentKeyDown)
        }
        ,
        t.prototype.updateSize = function() {
            var e = this.context.isRtl
              , t = this.props
              , n = t.alignmentEl
              , r = t.alignGridTop
              , o = this.rootEl
              , i = function(e) {
                for (var t = Lo(e), n = e.getBoundingClientRect(), r = 0, o = t; r < o.length; r++) {
                    var i = vo(n, o[r].getBoundingClientRect());
                    if (!i)
                        return null;
                    n = i
                }
                return n
            }(n);
            if (i) {
                var a = o.getBoundingClientRect()
                  , s = r ? Pe(n, ".fc-scrollgrid").getBoundingClientRect().top : i.top
                  , l = e ? i.right - a.width : i.left;
                s = Math.max(s, 10),
                l = Math.min(l, document.documentElement.clientWidth - 10 - a.width),
                l = Math.max(l, 10);
                var u = o.offsetParent.getBoundingClientRect();
                We(o, {
                    top: s - u.top,
                    left: l - u.left
                })
            }
        }
        ,
        t
    }(ii)
      , Ws = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.handleRootEl = function(e) {
                t.rootEl = e,
                e ? t.context.registerInteractiveComponent(t, {
                    el: e,
                    useEventCenter: !1
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.context
              , t = e.options
              , n = e.dateEnv
              , r = this.props
              , o = r.startDate
              , i = r.todayRange
              , a = r.dateProfile
              , s = n.format(o, t.dayPopoverFormat);
            return Yo(Ms, {
                date: o,
                dateProfile: a,
                todayRange: i,
                elRef: this.handleRootEl
            }, (function(e, t, n) {
                return Yo(As, {
                    elRef: e,
                    id: r.id,
                    title: s,
                    extraClassNames: ["fc-more-popover"].concat(t),
                    extraAttrs: n,
                    parentEl: r.parentEl,
                    alignmentEl: r.alignmentEl,
                    alignGridTop: r.alignGridTop,
                    onClose: r.onClose
                }, Yo(xs, {
                    date: o,
                    dateProfile: a,
                    todayRange: i
                }, (function(e, t) {
                    return t && Yo("div", {
                        className: "fc-more-popover-misc",
                        ref: e
                    }, t)
                }
                )), r.children)
            }
            ))
        }
        ,
        t.prototype.queryHit = function(e, t, n, o) {
            var i = this.rootEl
              , a = this.props;
            return e >= 0 && e < n && t >= 0 && t < o ? {
                dateProfile: a.dateProfile,
                dateSpan: r({
                    allDay: !0,
                    range: {
                        start: a.startDate,
                        end: a.endDate
                    }
                }, a.extraDateSpan),
                dayEl: i,
                rect: {
                    left: 0,
                    top: 0,
                    right: n,
                    bottom: o
                },
                layer: 1
            } : null
        }
        ,
        t
    }(ui)
      , Ls = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.linkElRef = Xo(),
            t.state = {
                isPopoverOpen: !1,
                popoverId: Ve()
            },
            t.handleClick = function(e) {
                var n = t
                  , r = n.props
                  , o = n.context
                  , i = o.options.moreLinkClick
                  , a = Bs(r).start;
                function s(e) {
                    var t = e.eventRange
                      , n = t.def
                      , r = t.instance
                      , i = t.range;
                    return {
                        event: new Zr(o,n,r),
                        start: o.dateEnv.toDate(i.start),
                        end: o.dateEnv.toDate(i.end),
                        isStart: e.isStart,
                        isEnd: e.isEnd
                    }
                }
                "function" == typeof i && (i = i({
                    date: a,
                    allDay: Boolean(r.allDayDate),
                    allSegs: r.allSegs.map(s),
                    hiddenSegs: r.hiddenSegs.map(s),
                    jsEvent: e,
                    view: o.viewApi
                })),
                i && "popover" !== i ? "string" == typeof i && o.calendarApi.zoomTo(a, i) : t.setState({
                    isPopoverOpen: !0
                })
            }
            ,
            t.handlePopoverClose = function() {
                t.setState({
                    isPopoverOpen: !1
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.state;
            return Yo(ni.Consumer, null, (function(r) {
                var o = r.viewApi
                  , i = r.options
                  , a = r.calendarApi
                  , s = i.moreLinkText
                  , l = t.moreCnt
                  , u = Bs(t)
                  , c = "function" == typeof s ? s.call(a, l) : "+" + l + " " + s
                  , d = lt(i.moreLinkHint, [l], c)
                  , p = {
                    num: l,
                    shortText: "+" + l,
                    text: c,
                    view: o
                };
                return Yo(Ko, null, Boolean(t.moreCnt) && Yo(hi, {
                    elRef: e.linkElRef,
                    hookProps: p,
                    classNames: i.moreLinkClassNames,
                    content: i.moreLinkContent,
                    defaultContent: t.defaultContent || Us,
                    didMount: i.moreLinkDidMount,
                    willUnmount: i.moreLinkWillUnmount
                }, (function(r, o, i, a) {
                    return t.children(r, ["fc-more-link"].concat(o), i, a, e.handleClick, d, n.isPopoverOpen, n.isPopoverOpen ? n.popoverId : "")
                }
                )), n.isPopoverOpen && Yo(Ws, {
                    id: n.popoverId,
                    startDate: u.start,
                    endDate: u.end,
                    dateProfile: t.dateProfile,
                    todayRange: t.todayRange,
                    extraDateSpan: t.extraDateSpan,
                    parentEl: e.parentEl,
                    alignmentEl: t.alignmentElRef.current,
                    alignGridTop: t.alignGridTop,
                    onClose: e.handlePopoverClose
                }, t.popoverContent()))
            }
            ))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateParentEl()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateParentEl()
        }
        ,
        t.prototype.updateParentEl = function() {
            this.linkElRef.current && (this.parentEl = Pe(this.linkElRef.current, ".fc-view-harness"))
        }
        ,
        t
    }(ii);
    function Us(e) {
        return e.text
    }
    function Bs(e) {
        if (e.allDayDate)
            return {
                start: e.allDayDate,
                end: ht(e.allDayDate, 1)
            };
        var t, n = e.hiddenSegs;
        return {
            start: zs(n),
            end: (t = n,
            t.reduce(Fs).eventRange.range.end)
        }
    }
    function zs(e) {
        return e.reduce(Vs).eventRange.range.start
    }
    function Vs(e, t) {
        return e.eventRange.range.start < t.eventRange.range.start ? e : t
    }
    function Fs(e, t) {
        return e.eventRange.range.end > t.eventRange.range.end ? e : t
    }
    var Gs = function(e) {
        function t(t, n) {
            void 0 === n && (n = {});
            var o = e.call(this) || this;
            return o.isRendering = !1,
            o.isRendered = !1,
            o.currentClassNames = [],
            o.customContentRenderId = 0,
            o.handleAction = function(e) {
                switch (e.type) {
                case "SET_EVENT_DRAG":
                case "SET_EVENT_RESIZE":
                    o.renderRunner.tryDrain()
                }
            }
            ,
            o.handleData = function(e) {
                o.currentData = e,
                o.renderRunner.request(e.calendarOptions.rerenderDelay)
            }
            ,
            o.handleRenderRequest = function() {
                if (o.isRendering) {
                    o.isRendered = !0;
                    var e = o.currentData;
                    Qo((function() {
                        Zo(Yo(Wa, {
                            options: e.calendarOptions,
                            theme: e.theme,
                            emitter: e.emitter
                        }, (function(t, n, i, a) {
                            return o.setClassNames(t),
                            o.setHeight(n),
                            Yo(vi.Provider, {
                                value: o.customContentRenderId
                            }, Yo(Ha, r({
                                isHeightAuto: i,
                                forPrint: a
                            }, e)))
                        }
                        )), o.el)
                    }
                    ))
                } else
                    o.isRendered && (o.isRendered = !1,
                    ei(o.el),
                    o.setClassNames([]),
                    o.setHeight(""))
            }
            ,
            o.el = t,
            o.renderRunner = new $i(o.handleRenderRequest),
            new ea({
                optionOverrides: n,
                calendarApi: o,
                onAction: o.handleAction,
                onData: o.handleData
            }),
            o
        }
        return n(t, e),
        Object.defineProperty(t.prototype, "view", {
            get: function() {
                return this.currentData.viewApi
            },
            enumerable: !1,
            configurable: !0
        }),
        t.prototype.render = function() {
            var e = this.isRendering;
            e ? this.customContentRenderId += 1 : this.isRendering = !0,
            this.renderRunner.request(),
            e && this.updateSize()
        }
        ,
        t.prototype.destroy = function() {
            this.isRendering && (this.isRendering = !1,
            this.renderRunner.request())
        }
        ,
        t.prototype.updateSize = function() {
            var t = this;
            Qo((function() {
                e.prototype.updateSize.call(t)
            }
            ))
        }
        ,
        t.prototype.batchRendering = function(e) {
            this.renderRunner.pause("batchRendering"),
            e(),
            this.renderRunner.resume("batchRendering")
        }
        ,
        t.prototype.pauseRendering = function() {
            this.renderRunner.pause("pauseRendering")
        }
        ,
        t.prototype.resumeRendering = function() {
            this.renderRunner.resume("pauseRendering", !0)
        }
        ,
        t.prototype.resetOptions = function(e, t) {
            this.currentDataManager.resetOptions(e, t)
        }
        ,
        t.prototype.setClassNames = function(e) {
            if (!un(e, this.currentClassNames)) {
                for (var t = this.el.classList, n = 0, r = this.currentClassNames; n < r.length; n++) {
                    var o = r[n];
                    t.remove(o)
                }
                for (var i = 0, a = e; i < a.length; i++) {
                    o = a[i];
                    t.add(o)
                }
                this.currentClassNames = e
            }
        }
        ,
        t.prototype.setHeight = function(e) {
            Le(this.el, "height", e)
        }
        ,
        t
    }(Yr);
    Ta.touchMouseIgnoreWait = 500;
    var js = 0
      , qs = 0
      , Ys = !1
      , Zs = function() {
        function e(e) {
            var t = this;
            this.subjectEl = null,
            this.selector = "",
            this.handleSelector = "",
            this.shouldIgnoreMove = !1,
            this.shouldWatchScroll = !0,
            this.isDragging = !1,
            this.isTouchDragging = !1,
            this.wasTouchScroll = !1,
            this.handleMouseDown = function(e) {
                if (!t.shouldIgnoreMouse() && function(e) {
                    return 0 === e.button && !e.ctrlKey
                }(e) && t.tryStart(e)) {
                    var n = t.createEventFromMouse(e, !0);
                    t.emitter.trigger("pointerdown", n),
                    t.initScrollWatch(n),
                    t.shouldIgnoreMove || document.addEventListener("mousemove", t.handleMouseMove),
                    document.addEventListener("mouseup", t.handleMouseUp)
                }
            }
            ,
            this.handleMouseMove = function(e) {
                var n = t.createEventFromMouse(e);
                t.recordCoords(n),
                t.emitter.trigger("pointermove", n)
            }
            ,
            this.handleMouseUp = function(e) {
                document.removeEventListener("mousemove", t.handleMouseMove),
                document.removeEventListener("mouseup", t.handleMouseUp),
                t.emitter.trigger("pointerup", t.createEventFromMouse(e)),
                t.cleanup()
            }
            ,
            this.handleTouchStart = function(e) {
                if (t.tryStart(e)) {
                    t.isTouchDragging = !0;
                    var n = t.createEventFromTouch(e, !0);
                    t.emitter.trigger("pointerdown", n),
                    t.initScrollWatch(n);
                    var r = e.target;
                    t.shouldIgnoreMove || r.addEventListener("touchmove", t.handleTouchMove),
                    r.addEventListener("touchend", t.handleTouchEnd),
                    r.addEventListener("touchcancel", t.handleTouchEnd),
                    window.addEventListener("scroll", t.handleTouchScroll, !0)
                }
            }
            ,
            this.handleTouchMove = function(e) {
                var n = t.createEventFromTouch(e);
                t.recordCoords(n),
                t.emitter.trigger("pointermove", n)
            }
            ,
            this.handleTouchEnd = function(e) {
                if (t.isDragging) {
                    var n = e.target;
                    n.removeEventListener("touchmove", t.handleTouchMove),
                    n.removeEventListener("touchend", t.handleTouchEnd),
                    n.removeEventListener("touchcancel", t.handleTouchEnd),
                    window.removeEventListener("scroll", t.handleTouchScroll, !0),
                    t.emitter.trigger("pointerup", t.createEventFromTouch(e)),
                    t.cleanup(),
                    t.isTouchDragging = !1,
                    js += 1,
                    setTimeout((function() {
                        js -= 1
                    }
                    ), Ta.touchMouseIgnoreWait)
                }
            }
            ,
            this.handleTouchScroll = function() {
                t.wasTouchScroll = !0
            }
            ,
            this.handleScroll = function(e) {
                if (!t.shouldIgnoreMove) {
                    var n = window.pageXOffset - t.prevScrollX + t.prevPageX
                      , r = window.pageYOffset - t.prevScrollY + t.prevPageY;
                    t.emitter.trigger("pointermove", {
                        origEvent: e,
                        isTouch: t.isTouchDragging,
                        subjectEl: t.subjectEl,
                        pageX: n,
                        pageY: r,
                        deltaX: n - t.origPageX,
                        deltaY: r - t.origPageY
                    })
                }
            }
            ,
            this.containerEl = e,
            this.emitter = new Bo,
            e.addEventListener("mousedown", this.handleMouseDown),
            e.addEventListener("touchstart", this.handleTouchStart, {
                passive: !0
            }),
            1 === (qs += 1) && window.addEventListener("touchmove", Xs, {
                passive: !1
            })
        }
        return e.prototype.destroy = function() {
            this.containerEl.removeEventListener("mousedown", this.handleMouseDown),
            this.containerEl.removeEventListener("touchstart", this.handleTouchStart, {
                passive: !0
            }),
            (qs -= 1) || window.removeEventListener("touchmove", Xs, {
                passive: !1
            })
        }
        ,
        e.prototype.tryStart = function(e) {
            var t = this.querySubjectEl(e)
              , n = e.target;
            return !(!t || this.handleSelector && !Pe(n, this.handleSelector)) && (this.subjectEl = t,
            this.isDragging = !0,
            this.wasTouchScroll = !1,
            !0)
        }
        ,
        e.prototype.cleanup = function() {
            Ys = !1,
            this.isDragging = !1,
            this.subjectEl = null,
            this.destroyScrollWatch()
        }
        ,
        e.prototype.querySubjectEl = function(e) {
            return this.selector ? Pe(e.target, this.selector) : this.containerEl
        }
        ,
        e.prototype.shouldIgnoreMouse = function() {
            return js || this.isTouchDragging
        }
        ,
        e.prototype.cancelTouchScroll = function() {
            this.isDragging && (Ys = !0)
        }
        ,
        e.prototype.initScrollWatch = function(e) {
            this.shouldWatchScroll && (this.recordCoords(e),
            window.addEventListener("scroll", this.handleScroll, !0))
        }
        ,
        e.prototype.recordCoords = function(e) {
            this.shouldWatchScroll && (this.prevPageX = e.pageX,
            this.prevPageY = e.pageY,
            this.prevScrollX = window.pageXOffset,
            this.prevScrollY = window.pageYOffset)
        }
        ,
        e.prototype.destroyScrollWatch = function() {
            this.shouldWatchScroll && window.removeEventListener("scroll", this.handleScroll, !0)
        }
        ,
        e.prototype.createEventFromMouse = function(e, t) {
            var n = 0
              , r = 0;
            return t ? (this.origPageX = e.pageX,
            this.origPageY = e.pageY) : (n = e.pageX - this.origPageX,
            r = e.pageY - this.origPageY),
            {
                origEvent: e,
                isTouch: !1,
                subjectEl: this.subjectEl,
                pageX: e.pageX,
                pageY: e.pageY,
                deltaX: n,
                deltaY: r
            }
        }
        ,
        e.prototype.createEventFromTouch = function(e, t) {
            var n, r, o = e.touches, i = 0, a = 0;
            return o && o.length ? (n = o[0].pageX,
            r = o[0].pageY) : (n = e.pageX,
            r = e.pageY),
            t ? (this.origPageX = n,
            this.origPageY = r) : (i = n - this.origPageX,
            a = r - this.origPageY),
            {
                origEvent: e,
                isTouch: !0,
                subjectEl: this.subjectEl,
                pageX: n,
                pageY: r,
                deltaX: i,
                deltaY: a
            }
        }
        ,
        e
    }();
    function Xs(e) {
        Ys && e.preventDefault()
    }
    var Ks = function() {
        function e() {
            this.isVisible = !1,
            this.sourceEl = null,
            this.mirrorEl = null,
            this.sourceElRect = null,
            this.parentNode = document.body,
            this.zIndex = 9999,
            this.revertDuration = 0
        }
        return e.prototype.start = function(e, t, n) {
            this.sourceEl = e,
            this.sourceElRect = this.sourceEl.getBoundingClientRect(),
            this.origScreenX = t - window.pageXOffset,
            this.origScreenY = n - window.pageYOffset,
            this.deltaX = 0,
            this.deltaY = 0,
            this.updateElPosition()
        }
        ,
        e.prototype.handleMove = function(e, t) {
            this.deltaX = e - window.pageXOffset - this.origScreenX,
            this.deltaY = t - window.pageYOffset - this.origScreenY,
            this.updateElPosition()
        }
        ,
        e.prototype.setIsVisible = function(e) {
            e ? this.isVisible || (this.mirrorEl && (this.mirrorEl.style.display = ""),
            this.isVisible = e,
            this.updateElPosition()) : this.isVisible && (this.mirrorEl && (this.mirrorEl.style.display = "none"),
            this.isVisible = e)
        }
        ,
        e.prototype.stop = function(e, t) {
            var n = this
              , r = function() {
                n.cleanup(),
                t()
            };
            e && this.mirrorEl && this.isVisible && this.revertDuration && (this.deltaX || this.deltaY) ? this.doRevertAnimation(r, this.revertDuration) : setTimeout(r, 0)
        }
        ,
        e.prototype.doRevertAnimation = function(e, t) {
            var n = this.mirrorEl
              , r = this.sourceEl.getBoundingClientRect();
            n.style.transition = "top " + t + "ms,left " + t + "ms",
            We(n, {
                left: r.left,
                top: r.top
            }),
            qe(n, (function() {
                n.style.transition = "",
                e()
            }
            ))
        }
        ,
        e.prototype.cleanup = function() {
            this.mirrorEl && (Ie(this.mirrorEl),
            this.mirrorEl = null),
            this.sourceEl = null
        }
        ,
        e.prototype.updateElPosition = function() {
            this.sourceEl && this.isVisible && We(this.getMirrorEl(), {
                left: this.sourceElRect.left + this.deltaX,
                top: this.sourceElRect.top + this.deltaY
            })
        }
        ,
        e.prototype.getMirrorEl = function() {
            var e = this.sourceElRect
              , t = this.mirrorEl;
            return t || ((t = this.mirrorEl = this.sourceEl.cloneNode(!0)).classList.add("fc-unselectable"),
            t.classList.add("fc-event-dragging"),
            We(t, {
                position: "fixed",
                zIndex: this.zIndex,
                visibility: "",
                boxSizing: "border-box",
                width: e.right - e.left,
                height: e.bottom - e.top,
                right: "auto",
                bottom: "auto",
                margin: 0
            }),
            this.parentNode.appendChild(t)),
            t
        }
        ,
        e
    }()
      , $s = function(e) {
        function t(t, n) {
            var r = e.call(this) || this;
            return r.handleScroll = function() {
                r.scrollTop = r.scrollController.getScrollTop(),
                r.scrollLeft = r.scrollController.getScrollLeft(),
                r.handleScrollChange()
            }
            ,
            r.scrollController = t,
            r.doesListening = n,
            r.scrollTop = r.origScrollTop = t.getScrollTop(),
            r.scrollLeft = r.origScrollLeft = t.getScrollLeft(),
            r.scrollWidth = t.getScrollWidth(),
            r.scrollHeight = t.getScrollHeight(),
            r.clientWidth = t.getClientWidth(),
            r.clientHeight = t.getClientHeight(),
            r.clientRect = r.computeClientRect(),
            r.doesListening && r.getEventTarget().addEventListener("scroll", r.handleScroll),
            r
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.doesListening && this.getEventTarget().removeEventListener("scroll", this.handleScroll)
        }
        ,
        t.prototype.getScrollTop = function() {
            return this.scrollTop
        }
        ,
        t.prototype.getScrollLeft = function() {
            return this.scrollLeft
        }
        ,
        t.prototype.setScrollTop = function(e) {
            this.scrollController.setScrollTop(e),
            this.doesListening || (this.scrollTop = Math.max(Math.min(e, this.getMaxScrollTop()), 0),
            this.handleScrollChange())
        }
        ,
        t.prototype.setScrollLeft = function(e) {
            this.scrollController.setScrollLeft(e),
            this.doesListening || (this.scrollLeft = Math.max(Math.min(e, this.getMaxScrollLeft()), 0),
            this.handleScrollChange())
        }
        ,
        t.prototype.getClientWidth = function() {
            return this.clientWidth
        }
        ,
        t.prototype.getClientHeight = function() {
            return this.clientHeight
        }
        ,
        t.prototype.getScrollWidth = function() {
            return this.scrollWidth
        }
        ,
        t.prototype.getScrollHeight = function() {
            return this.scrollHeight
        }
        ,
        t.prototype.handleScrollChange = function() {}
        ,
        t
    }(Vo)
      , Js = function(e) {
        function t(t, n) {
            return e.call(this, new Fo(t), n) || this
        }
        return n(t, e),
        t.prototype.getEventTarget = function() {
            return this.scrollController.el
        }
        ,
        t.prototype.computeClientRect = function() {
            return Ao(this.scrollController.el)
        }
        ,
        t
    }($s)
      , Qs = function(e) {
        function t(t) {
            return e.call(this, new Go, t) || this
        }
        return n(t, e),
        t.prototype.getEventTarget = function() {
            return window
        }
        ,
        t.prototype.computeClientRect = function() {
            return {
                left: this.scrollLeft,
                right: this.scrollLeft + this.clientWidth,
                top: this.scrollTop,
                bottom: this.scrollTop + this.clientHeight
            }
        }
        ,
        t.prototype.handleScrollChange = function() {
            this.clientRect = this.computeClientRect()
        }
        ,
        t
    }($s)
      , el = "function" == typeof performance ? performance.now : Date.now
      , tl = function() {
        function e() {
            var e = this;
            this.isEnabled = !0,
            this.scrollQuery = [window, ".fc-scroller"],
            this.edgeThreshold = 50,
            this.maxVelocity = 300,
            this.pointerScreenX = null,
            this.pointerScreenY = null,
            this.isAnimating = !1,
            this.scrollCaches = null,
            this.everMovedUp = !1,
            this.everMovedDown = !1,
            this.everMovedLeft = !1,
            this.everMovedRight = !1,
            this.animate = function() {
                if (e.isAnimating) {
                    var t = e.computeBestEdge(e.pointerScreenX + window.pageXOffset, e.pointerScreenY + window.pageYOffset);
                    if (t) {
                        var n = el();
                        e.handleSide(t, (n - e.msSinceRequest) / 1e3),
                        e.requestAnimation(n)
                    } else
                        e.isAnimating = !1
                }
            }
        }
        return e.prototype.start = function(e, t, n) {
            this.isEnabled && (this.scrollCaches = this.buildCaches(n),
            this.pointerScreenX = null,
            this.pointerScreenY = null,
            this.everMovedUp = !1,
            this.everMovedDown = !1,
            this.everMovedLeft = !1,
            this.everMovedRight = !1,
            this.handleMove(e, t))
        }
        ,
        e.prototype.handleMove = function(e, t) {
            if (this.isEnabled) {
                var n = e - window.pageXOffset
                  , r = t - window.pageYOffset
                  , o = null === this.pointerScreenY ? 0 : r - this.pointerScreenY
                  , i = null === this.pointerScreenX ? 0 : n - this.pointerScreenX;
                o < 0 ? this.everMovedUp = !0 : o > 0 && (this.everMovedDown = !0),
                i < 0 ? this.everMovedLeft = !0 : i > 0 && (this.everMovedRight = !0),
                this.pointerScreenX = n,
                this.pointerScreenY = r,
                this.isAnimating || (this.isAnimating = !0,
                this.requestAnimation(el()))
            }
        }
        ,
        e.prototype.stop = function() {
            if (this.isEnabled) {
                this.isAnimating = !1;
                for (var e = 0, t = this.scrollCaches; e < t.length; e++) {
                    t[e].destroy()
                }
                this.scrollCaches = null
            }
        }
        ,
        e.prototype.requestAnimation = function(e) {
            this.msSinceRequest = e,
            requestAnimationFrame(this.animate)
        }
        ,
        e.prototype.handleSide = function(e, t) {
            var n = e.scrollCache
              , r = this.edgeThreshold
              , o = r - e.distance
              , i = o * o / (r * r) * this.maxVelocity * t
              , a = 1;
            switch (e.name) {
            case "left":
                a = -1;
            case "right":
                n.setScrollLeft(n.getScrollLeft() + i * a);
                break;
            case "top":
                a = -1;
            case "bottom":
                n.setScrollTop(n.getScrollTop() + i * a)
            }
        }
        ,
        e.prototype.computeBestEdge = function(e, t) {
            for (var n = this.edgeThreshold, r = null, o = 0, i = this.scrollCaches || []; o < i.length; o++) {
                var a = i[o]
                  , s = a.clientRect
                  , l = e - s.left
                  , u = s.right - e
                  , c = t - s.top
                  , d = s.bottom - t;
                l >= 0 && u >= 0 && c >= 0 && d >= 0 && (c <= n && this.everMovedUp && a.canScrollUp() && (!r || r.distance > c) && (r = {
                    scrollCache: a,
                    name: "top",
                    distance: c
                }),
                d <= n && this.everMovedDown && a.canScrollDown() && (!r || r.distance > d) && (r = {
                    scrollCache: a,
                    name: "bottom",
                    distance: d
                }),
                l <= n && this.everMovedLeft && a.canScrollLeft() && (!r || r.distance > l) && (r = {
                    scrollCache: a,
                    name: "left",
                    distance: l
                }),
                u <= n && this.everMovedRight && a.canScrollRight() && (!r || r.distance > u) && (r = {
                    scrollCache: a,
                    name: "right",
                    distance: u
                }))
            }
            return r
        }
        ,
        e.prototype.buildCaches = function(e) {
            return this.queryScrollEls(e).map((function(e) {
                return e === window ? new Qs(!1) : new Js(e,!1)
            }
            ))
        }
        ,
        e.prototype.queryScrollEls = function(e) {
            for (var t = [], n = 0, r = this.scrollQuery; n < r.length; n++) {
                var o = r[n];
                "object" == typeof o ? t.push(o) : t.push.apply(t, Array.prototype.slice.call(Be(e).querySelectorAll(o)))
            }
            return t
        }
        ,
        e
    }()
      , nl = function(e) {
        function t(t, n) {
            var r = e.call(this, t) || this;
            r.containerEl = t,
            r.delay = null,
            r.minDistance = 0,
            r.touchScrollAllowed = !0,
            r.mirrorNeedsRevert = !1,
            r.isInteracting = !1,
            r.isDragging = !1,
            r.isDelayEnded = !1,
            r.isDistanceSurpassed = !1,
            r.delayTimeoutId = null,
            r.onPointerDown = function(e) {
                r.isDragging || (r.isInteracting = !0,
                r.isDelayEnded = !1,
                r.isDistanceSurpassed = !1,
                Qe(document.body),
                tt(document.body),
                e.isTouch || e.origEvent.preventDefault(),
                r.emitter.trigger("pointerdown", e),
                r.isInteracting && !r.pointer.shouldIgnoreMove && (r.mirror.setIsVisible(!1),
                r.mirror.start(e.subjectEl, e.pageX, e.pageY),
                r.startDelay(e),
                r.minDistance || r.handleDistanceSurpassed(e)))
            }
            ,
            r.onPointerMove = function(e) {
                if (r.isInteracting) {
                    if (r.emitter.trigger("pointermove", e),
                    !r.isDistanceSurpassed) {
                        var t = r.minDistance
                          , n = e.deltaX
                          , o = e.deltaY;
                        n * n + o * o >= t * t && r.handleDistanceSurpassed(e)
                    }
                    r.isDragging && ("scroll" !== e.origEvent.type && (r.mirror.handleMove(e.pageX, e.pageY),
                    r.autoScroller.handleMove(e.pageX, e.pageY)),
                    r.emitter.trigger("dragmove", e))
                }
            }
            ,
            r.onPointerUp = function(e) {
                r.isInteracting && (r.isInteracting = !1,
                et(document.body),
                nt(document.body),
                r.emitter.trigger("pointerup", e),
                r.isDragging && (r.autoScroller.stop(),
                r.tryStopDrag(e)),
                r.delayTimeoutId && (clearTimeout(r.delayTimeoutId),
                r.delayTimeoutId = null))
            }
            ;
            var o = r.pointer = new Zs(t);
            return o.emitter.on("pointerdown", r.onPointerDown),
            o.emitter.on("pointermove", r.onPointerMove),
            o.emitter.on("pointerup", r.onPointerUp),
            n && (o.selector = n),
            r.mirror = new Ks,
            r.autoScroller = new tl,
            r
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.pointer.destroy(),
            this.onPointerUp({})
        }
        ,
        t.prototype.startDelay = function(e) {
            var t = this;
            "number" == typeof this.delay ? this.delayTimeoutId = setTimeout((function() {
                t.delayTimeoutId = null,
                t.handleDelayEnd(e)
            }
            ), this.delay) : this.handleDelayEnd(e)
        }
        ,
        t.prototype.handleDelayEnd = function(e) {
            this.isDelayEnded = !0,
            this.tryStartDrag(e)
        }
        ,
        t.prototype.handleDistanceSurpassed = function(e) {
            this.isDistanceSurpassed = !0,
            this.tryStartDrag(e)
        }
        ,
        t.prototype.tryStartDrag = function(e) {
            this.isDelayEnded && this.isDistanceSurpassed && (this.pointer.wasTouchScroll && !this.touchScrollAllowed || (this.isDragging = !0,
            this.mirrorNeedsRevert = !1,
            this.autoScroller.start(e.pageX, e.pageY, this.containerEl),
            this.emitter.trigger("dragstart", e),
            !1 === this.touchScrollAllowed && this.pointer.cancelTouchScroll()))
        }
        ,
        t.prototype.tryStopDrag = function(e) {
            this.mirror.stop(this.mirrorNeedsRevert, this.stopDrag.bind(this, e))
        }
        ,
        t.prototype.stopDrag = function(e) {
            this.isDragging = !1,
            this.emitter.trigger("dragend", e)
        }
        ,
        t.prototype.setIgnoreMove = function(e) {
            this.pointer.shouldIgnoreMove = e
        }
        ,
        t.prototype.setMirrorIsVisible = function(e) {
            this.mirror.setIsVisible(e)
        }
        ,
        t.prototype.setMirrorNeedsRevert = function(e) {
            this.mirrorNeedsRevert = e
        }
        ,
        t.prototype.setAutoScrollEnabled = function(e) {
            this.autoScroller.isEnabled = e
        }
        ,
        t
    }(wa)
      , rl = function() {
        function e(e) {
            this.origRect = Wo(e),
            this.scrollCaches = Lo(e).map((function(e) {
                return new Js(e,!0)
            }
            ))
        }
        return e.prototype.destroy = function() {
            for (var e = 0, t = this.scrollCaches; e < t.length; e++) {
                t[e].destroy()
            }
        }
        ,
        e.prototype.computeLeft = function() {
            for (var e = this.origRect.left, t = 0, n = this.scrollCaches; t < n.length; t++) {
                var r = n[t];
                e += r.origScrollLeft - r.getScrollLeft()
            }
            return e
        }
        ,
        e.prototype.computeTop = function() {
            for (var e = this.origRect.top, t = 0, n = this.scrollCaches; t < n.length; t++) {
                var r = n[t];
                e += r.origScrollTop - r.getScrollTop()
            }
            return e
        }
        ,
        e.prototype.isWithinClipping = function(e, t) {
            for (var n, r, o = {
                left: e,
                top: t
            }, i = 0, a = this.scrollCaches; i < a.length; i++) {
                var s = a[i];
                if (n = s.getEventTarget(),
                r = void 0,
                "HTML" !== (r = n.tagName) && "BODY" !== r && !ho(o, s.clientRect))
                    return !1
            }
            return !0
        }
        ,
        e
    }();
    var ol = function() {
        function e(e, t) {
            var n = this;
            this.useSubjectCenter = !1,
            this.requireInitial = !0,
            this.initialHit = null,
            this.movingHit = null,
            this.finalHit = null,
            this.handlePointerDown = function(e) {
                var t = n.dragging;
                n.initialHit = null,
                n.movingHit = null,
                n.finalHit = null,
                n.prepareHits(),
                n.processFirstCoord(e),
                n.initialHit || !n.requireInitial ? (t.setIgnoreMove(!1),
                n.emitter.trigger("pointerdown", e)) : t.setIgnoreMove(!0)
            }
            ,
            this.handleDragStart = function(e) {
                n.emitter.trigger("dragstart", e),
                n.handleMove(e, !0)
            }
            ,
            this.handleDragMove = function(e) {
                n.emitter.trigger("dragmove", e),
                n.handleMove(e)
            }
            ,
            this.handlePointerUp = function(e) {
                n.releaseHits(),
                n.emitter.trigger("pointerup", e)
            }
            ,
            this.handleDragEnd = function(e) {
                n.movingHit && n.emitter.trigger("hitupdate", null, !0, e),
                n.finalHit = n.movingHit,
                n.movingHit = null,
                n.emitter.trigger("dragend", e)
            }
            ,
            this.droppableStore = t,
            e.emitter.on("pointerdown", this.handlePointerDown),
            e.emitter.on("dragstart", this.handleDragStart),
            e.emitter.on("dragmove", this.handleDragMove),
            e.emitter.on("pointerup", this.handlePointerUp),
            e.emitter.on("dragend", this.handleDragEnd),
            this.dragging = e,
            this.emitter = new Bo
        }
        return e.prototype.processFirstCoord = function(e) {
            var t, n = {
                left: e.pageX,
                top: e.pageY
            }, r = n, o = e.subjectEl;
            o instanceof HTMLElement && (r = mo(r, t = Wo(o)));
            var i = this.initialHit = this.queryHitForOffset(r.left, r.top);
            if (i) {
                if (this.useSubjectCenter && t) {
                    var a = vo(t, i.rect);
                    a && (r = yo(a))
                }
                this.coordAdjust = So(r, n)
            } else
                this.coordAdjust = {
                    left: 0,
                    top: 0
                }
        }
        ,
        e.prototype.handleMove = function(e, t) {
            var n = this.queryHitForOffset(e.pageX + this.coordAdjust.left, e.pageY + this.coordAdjust.top);
            !t && il(this.movingHit, n) || (this.movingHit = n,
            this.emitter.trigger("hitupdate", n, !1, e))
        }
        ,
        e.prototype.prepareHits = function() {
            this.offsetTrackers = Ht(this.droppableStore, (function(e) {
                return e.component.prepareHits(),
                new rl(e.el)
            }
            ))
        }
        ,
        e.prototype.releaseHits = function() {
            var e = this.offsetTrackers;
            for (var t in e)
                e[t].destroy();
            this.offsetTrackers = {}
        }
        ,
        e.prototype.queryHitForOffset = function(e, t) {
            var n = this.droppableStore
              , r = this.offsetTrackers
              , o = null;
            for (var i in n) {
                var a = n[i].component
                  , s = r[i];
                if (s && s.isWithinClipping(e, t)) {
                    var l = s.computeLeft()
                      , u = s.computeTop()
                      , c = e - l
                      , d = t - u
                      , p = s.origRect
                      , f = p.right - p.left
                      , h = p.bottom - p.top;
                    if (c >= 0 && c < f && d >= 0 && d < h) {
                        var v = a.queryHit(c, d, f, h);
                        v && pr(v.dateProfile.activeRange, v.dateSpan.range) && (!o || v.layer > o.layer) && (v.componentId = i,
                        v.context = a.context,
                        v.rect.left += l,
                        v.rect.right += l,
                        v.rect.top += u,
                        v.rect.bottom += u,
                        o = v)
                    }
                }
            }
            return o
        }
        ,
        e
    }();
    function il(e, t) {
        return !e && !t || Boolean(e) === Boolean(t) && Pr(e.dateSpan, t.dateSpan)
    }
    function al(e, t) {
        for (var n, o, i = {}, a = 0, s = t.pluginHooks.datePointTransforms; a < s.length; a++) {
            var l = s[a];
            r(i, l(e, t))
        }
        return r(i, (n = e,
        {
            date: (o = t.dateEnv).toDate(n.range.start),
            dateStr: o.formatIso(n.range.start, {
                omitTime: n.allDay
            }),
            allDay: n.allDay
        })),
        i
    }
    var sl = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            n.handlePointerDown = function(e) {
                var t = n.dragging
                  , r = e.origEvent.target;
                t.setIgnoreMove(!n.component.isValidDateDownEl(r))
            }
            ,
            n.handleDragEnd = function(e) {
                var t = n.component;
                if (!n.dragging.pointer.wasTouchScroll) {
                    var o = n.hitDragging
                      , i = o.initialHit
                      , a = o.finalHit;
                    if (i && a && il(i, a)) {
                        var s = t.context
                          , l = r(r({}, al(i.dateSpan, s)), {
                            dayEl: i.dayEl,
                            jsEvent: e.origEvent,
                            view: s.viewApi || s.calendarApi.view
                        });
                        s.emitter.trigger("dateClick", l)
                    }
                }
            }
            ,
            n.dragging = new nl(t.el),
            n.dragging.autoScroller.isEnabled = !1;
            var o = n.hitDragging = new ol(n.dragging,Da(t));
            return o.emitter.on("pointerdown", n.handlePointerDown),
            o.emitter.on("dragend", n.handleDragEnd),
            n
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        t
    }(ba)
      , ll = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            n.dragSelection = null,
            n.handlePointerDown = function(e) {
                var t = n
                  , r = t.component
                  , o = t.dragging
                  , i = r.context.options.selectable && r.isValidDateDownEl(e.origEvent.target);
                o.setIgnoreMove(!i),
                o.delay = e.isTouch ? function(e) {
                    var t = e.context.options
                      , n = t.selectLongPressDelay;
                    null == n && (n = t.longPressDelay);
                    return n
                }(r) : null
            }
            ,
            n.handleDragStart = function(e) {
                n.component.context.calendarApi.unselect(e)
            }
            ,
            n.handleHitUpdate = function(e, t) {
                var o = n.component.context
                  , i = null
                  , a = !1;
                if (e) {
                    var s = n.hitDragging.initialHit;
                    e.componentId === s.componentId && n.isHitComboAllowed && !n.isHitComboAllowed(s, e) || (i = function(e, t, n) {
                        var o = e.dateSpan
                          , i = t.dateSpan
                          , a = [o.range.start, o.range.end, i.range.start, i.range.end];
                        a.sort(ut);
                        for (var s = {}, l = 0, u = n; l < u.length; l++) {
                            var c = (0,
                            u[l])(e, t);
                            if (!1 === c)
                                return null;
                            c && r(s, c)
                        }
                        return s.range = {
                            start: a[0],
                            end: a[3]
                        },
                        s.allDay = o.allDay,
                        s
                    }(s, e, o.pluginHooks.dateSelectionTransformers)),
                    i && Qa(i, e.dateProfile, o) || (a = !0,
                    i = null)
                }
                i ? o.dispatch({
                    type: "SELECT_DATES",
                    selection: i
                }) : t || o.dispatch({
                    type: "UNSELECT_DATES"
                }),
                a ? $e() : Je(),
                t || (n.dragSelection = i)
            }
            ,
            n.handlePointerUp = function(e) {
                n.dragSelection && (Ar(n.dragSelection, e, n.component.context),
                n.dragSelection = null)
            }
            ;
            var o = t.component.context.options
              , i = n.dragging = new nl(t.el);
            i.touchScrollAllowed = !1,
            i.minDistance = o.selectMinDistance || 0,
            i.autoScroller.isEnabled = o.dragScroll;
            var a = n.hitDragging = new ol(n.dragging,Da(t));
            return a.emitter.on("pointerdown", n.handlePointerDown),
            a.emitter.on("dragstart", n.handleDragStart),
            a.emitter.on("hitupdate", n.handleHitUpdate),
            a.emitter.on("pointerup", n.handlePointerUp),
            n
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        t
    }(ba);
    var ul = function(e) {
        function t(n) {
            var o = e.call(this, n) || this;
            o.subjectEl = null,
            o.subjectSeg = null,
            o.isDragging = !1,
            o.eventRange = null,
            o.relevantEvents = null,
            o.receivingContext = null,
            o.validMutation = null,
            o.mutatedRelevantEvents = null,
            o.handlePointerDown = function(e) {
                var t = e.origEvent.target
                  , n = o
                  , r = n.component
                  , i = n.dragging
                  , a = i.mirror
                  , s = r.context.options
                  , l = r.context;
                o.subjectEl = e.subjectEl;
                var u = o.subjectSeg = mr(e.subjectEl)
                  , c = (o.eventRange = u.eventRange).instance.instanceId;
                o.relevantEvents = Bn(l.getCurrentData().eventStore, c),
                i.minDistance = e.isTouch ? 0 : s.eventDragMinDistance,
                i.delay = e.isTouch && c !== r.props.eventSelection ? function(e) {
                    var t = e.context.options
                      , n = t.eventLongPressDelay;
                    null == n && (n = t.longPressDelay);
                    return n
                }(r) : null,
                s.fixedMirrorParent ? a.parentNode = s.fixedMirrorParent : a.parentNode = Pe(t, ".fc"),
                a.revertDuration = s.dragRevertDuration;
                var d = r.isValidSegDownEl(t) && !Pe(t, ".fc-event-resizer");
                i.setIgnoreMove(!d),
                o.isDragging = d && e.subjectEl.classList.contains("fc-event-draggable")
            }
            ,
            o.handleDragStart = function(e) {
                var t = o.component.context
                  , n = o.eventRange
                  , r = n.instance.instanceId;
                e.isTouch ? r !== o.component.props.eventSelection && t.dispatch({
                    type: "SELECT_EVENT",
                    eventInstanceId: r
                }) : t.dispatch({
                    type: "UNSELECT_EVENT"
                }),
                o.isDragging && (t.calendarApi.unselect(e),
                t.emitter.trigger("eventDragStart", {
                    el: o.subjectEl,
                    event: new Zr(t,n.def,n.instance),
                    jsEvent: e.origEvent,
                    view: t.viewApi
                }))
            }
            ,
            o.handleHitUpdate = function(e, t) {
                if (o.isDragging) {
                    var n = o.relevantEvents
                      , r = o.hitDragging.initialHit
                      , i = o.component.context
                      , a = null
                      , s = null
                      , l = null
                      , u = !1
                      , c = {
                        affectedEvents: n,
                        mutatedEvents: {
                            defs: {},
                            instances: {}
                        },
                        isEvent: !0
                    };
                    if (e) {
                        var d = (a = e.context).options;
                        i === a || d.editable && d.droppable ? (s = function(e, t, n) {
                            var r = e.dateSpan
                              , o = t.dateSpan
                              , i = r.range.start
                              , a = o.range.start
                              , s = {};
                            r.allDay !== o.allDay && (s.allDay = o.allDay,
                            s.hasEnd = t.context.options.allDayMaintainDuration,
                            o.allDay && (i = bt(i)));
                            var l = ar(i, a, e.context.dateEnv, e.componentId === t.componentId ? e.largeUnit : null);
                            l.milliseconds && (s.allDay = !1);
                            for (var u = {
                                datesDelta: l,
                                standardProps: s
                            }, c = 0, d = n; c < d.length; c++) {
                                (0,
                                d[c])(u, e, t)
                            }
                            return u
                        }(r, e, a.getCurrentData().pluginHooks.eventDragMutationMassagers)) && (l = Ur(n, a.getCurrentData().eventUiBases, s, a),
                        c.mutatedEvents = l,
                        Ja(c, e.dateProfile, a) || (u = !0,
                        s = null,
                        l = null,
                        c.mutatedEvents = {
                            defs: {},
                            instances: {}
                        })) : a = null
                    }
                    o.displayDrag(a, c),
                    u ? $e() : Je(),
                    t || (i === a && il(r, e) && (s = null),
                    o.dragging.setMirrorNeedsRevert(!s),
                    o.dragging.setMirrorIsVisible(!e || !Be(o.subjectEl).querySelector(".fc-event-mirror")),
                    o.receivingContext = a,
                    o.validMutation = s,
                    o.mutatedRelevantEvents = l)
                }
            }
            ,
            o.handlePointerUp = function() {
                o.isDragging || o.cleanup()
            }
            ,
            o.handleDragEnd = function(e) {
                if (o.isDragging) {
                    var t = o.component.context
                      , n = t.viewApi
                      , i = o
                      , a = i.receivingContext
                      , s = i.validMutation
                      , l = o.eventRange.def
                      , u = o.eventRange.instance
                      , c = new Zr(t,l,u)
                      , d = o.relevantEvents
                      , p = o.mutatedRelevantEvents
                      , f = o.hitDragging.finalHit;
                    if (o.clearDrag(),
                    t.emitter.trigger("eventDragStop", {
                        el: o.subjectEl,
                        event: c,
                        jsEvent: e.origEvent,
                        view: n
                    }),
                    s) {
                        if (a === t) {
                            var h = new Zr(t,p.defs[l.defId],u ? p.instances[u.instanceId] : null);
                            t.dispatch({
                                type: "MERGE_EVENTS",
                                eventStore: p
                            });
                            for (var v = {
                                oldEvent: c,
                                event: h,
                                relatedEvents: Kr(p, t, u),
                                revert: function() {
                                    t.dispatch({
                                        type: "MERGE_EVENTS",
                                        eventStore: d
                                    })
                                }
                            }, g = {}, m = 0, y = t.getCurrentData().pluginHooks.eventDropTransformers; m < y.length; m++) {
                                var S = y[m];
                                r(g, S(s, t))
                            }
                            t.emitter.trigger("eventDrop", r(r(r({}, v), g), {
                                el: e.subjectEl,
                                delta: s.datesDelta,
                                jsEvent: e.origEvent,
                                view: n
                            })),
                            t.emitter.trigger("eventChange", v)
                        } else if (a) {
                            var E = {
                                event: c,
                                relatedEvents: Kr(d, t, u),
                                revert: function() {
                                    t.dispatch({
                                        type: "MERGE_EVENTS",
                                        eventStore: d
                                    })
                                }
                            };
                            t.emitter.trigger("eventLeave", r(r({}, E), {
                                draggedEl: e.subjectEl,
                                view: n
                            })),
                            t.dispatch({
                                type: "REMOVE_EVENTS",
                                eventStore: d
                            }),
                            t.emitter.trigger("eventRemove", E);
                            var b = p.defs[l.defId]
                              , C = p.instances[u.instanceId]
                              , D = new Zr(a,b,C);
                            a.dispatch({
                                type: "MERGE_EVENTS",
                                eventStore: p
                            });
                            var R = {
                                event: D,
                                relatedEvents: Kr(p, a, C),
                                revert: function() {
                                    a.dispatch({
                                        type: "REMOVE_EVENTS",
                                        eventStore: p
                                    })
                                }
                            };
                            a.emitter.trigger("eventAdd", R),
                            e.isTouch && a.dispatch({
                                type: "SELECT_EVENT",
                                eventInstanceId: u.instanceId
                            }),
                            a.emitter.trigger("drop", r(r({}, al(f.dateSpan, a)), {
                                draggedEl: e.subjectEl,
                                jsEvent: e.origEvent,
                                view: f.context.viewApi
                            })),
                            a.emitter.trigger("eventReceive", r(r({}, R), {
                                draggedEl: e.subjectEl,
                                view: f.context.viewApi
                            }))
                        }
                    } else
                        t.emitter.trigger("_noEventDrop")
                }
                o.cleanup()
            }
            ;
            var i = o.component.context.options
              , a = o.dragging = new nl(n.el);
            a.pointer.selector = t.SELECTOR,
            a.touchScrollAllowed = !1,
            a.autoScroller.isEnabled = i.dragScroll;
            var s = o.hitDragging = new ol(o.dragging,Ra);
            return s.useSubjectCenter = n.useEventCenter,
            s.emitter.on("pointerdown", o.handlePointerDown),
            s.emitter.on("dragstart", o.handleDragStart),
            s.emitter.on("hitupdate", o.handleHitUpdate),
            s.emitter.on("pointerup", o.handlePointerUp),
            s.emitter.on("dragend", o.handleDragEnd),
            o
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        t.prototype.displayDrag = function(e, t) {
            var n = this.component.context
              , r = this.receivingContext;
            r && r !== e && (r === n ? r.dispatch({
                type: "SET_EVENT_DRAG",
                state: {
                    affectedEvents: t.affectedEvents,
                    mutatedEvents: {
                        defs: {},
                        instances: {}
                    },
                    isEvent: !0
                }
            }) : r.dispatch({
                type: "UNSET_EVENT_DRAG"
            })),
            e && e.dispatch({
                type: "SET_EVENT_DRAG",
                state: t
            })
        }
        ,
        t.prototype.clearDrag = function() {
            var e = this.component.context
              , t = this.receivingContext;
            t && t.dispatch({
                type: "UNSET_EVENT_DRAG"
            }),
            e !== t && e.dispatch({
                type: "UNSET_EVENT_DRAG"
            })
        }
        ,
        t.prototype.cleanup = function() {
            this.subjectSeg = null,
            this.isDragging = !1,
            this.eventRange = null,
            this.relevantEvents = null,
            this.receivingContext = null,
            this.validMutation = null,
            this.mutatedRelevantEvents = null
        }
        ,
        t.SELECTOR = ".fc-event-draggable, .fc-event-resizable",
        t
    }(ba);
    var cl = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            n.draggingSegEl = null,
            n.draggingSeg = null,
            n.eventRange = null,
            n.relevantEvents = null,
            n.validMutation = null,
            n.mutatedRelevantEvents = null,
            n.handlePointerDown = function(e) {
                var t = n.component
                  , r = mr(n.querySegEl(e))
                  , o = n.eventRange = r.eventRange;
                n.dragging.minDistance = t.context.options.eventDragMinDistance,
                n.dragging.setIgnoreMove(!n.component.isValidSegDownEl(e.origEvent.target) || e.isTouch && n.component.props.eventSelection !== o.instance.instanceId)
            }
            ,
            n.handleDragStart = function(e) {
                var t = n.component.context
                  , r = n.eventRange;
                n.relevantEvents = Bn(t.getCurrentData().eventStore, n.eventRange.instance.instanceId);
                var o = n.querySegEl(e);
                n.draggingSegEl = o,
                n.draggingSeg = mr(o),
                t.calendarApi.unselect(),
                t.emitter.trigger("eventResizeStart", {
                    el: o,
                    event: new Zr(t,r.def,r.instance),
                    jsEvent: e.origEvent,
                    view: t.viewApi
                })
            }
            ,
            n.handleHitUpdate = function(e, t, r) {
                var o = n.component.context
                  , i = n.relevantEvents
                  , a = n.hitDragging.initialHit
                  , s = n.eventRange.instance
                  , l = null
                  , u = null
                  , c = !1
                  , d = {
                    affectedEvents: i,
                    mutatedEvents: {
                        defs: {},
                        instances: {}
                    },
                    isEvent: !0
                };
                e && (e.componentId === a.componentId && n.isHitComboAllowed && !n.isHitComboAllowed(a, e) || (l = function(e, t, n, r) {
                    var o = e.context.dateEnv
                      , i = e.dateSpan.range.start
                      , a = t.dateSpan.range.start
                      , s = ar(i, a, o, e.largeUnit);
                    if (n) {
                        if (o.add(r.start, s) < r.end)
                            return {
                                startDelta: s
                            }
                    } else if (o.add(r.end, s) > r.start)
                        return {
                            endDelta: s
                        };
                    return null
                }(a, e, r.subjectEl.classList.contains("fc-event-resizer-start"), s.range)));
                l && (u = Ur(i, o.getCurrentData().eventUiBases, l, o),
                d.mutatedEvents = u,
                Ja(d, e.dateProfile, o) || (c = !0,
                l = null,
                u = null,
                d.mutatedEvents = null)),
                u ? o.dispatch({
                    type: "SET_EVENT_RESIZE",
                    state: d
                }) : o.dispatch({
                    type: "UNSET_EVENT_RESIZE"
                }),
                c ? $e() : Je(),
                t || (l && il(a, e) && (l = null),
                n.validMutation = l,
                n.mutatedRelevantEvents = u)
            }
            ,
            n.handleDragEnd = function(e) {
                var t = n.component.context
                  , o = n.eventRange.def
                  , i = n.eventRange.instance
                  , a = new Zr(t,o,i)
                  , s = n.relevantEvents
                  , l = n.mutatedRelevantEvents;
                if (t.emitter.trigger("eventResizeStop", {
                    el: n.draggingSegEl,
                    event: a,
                    jsEvent: e.origEvent,
                    view: t.viewApi
                }),
                n.validMutation) {
                    var u = new Zr(t,l.defs[o.defId],i ? l.instances[i.instanceId] : null);
                    t.dispatch({
                        type: "MERGE_EVENTS",
                        eventStore: l
                    });
                    var c = {
                        oldEvent: a,
                        event: u,
                        relatedEvents: Kr(l, t, i),
                        revert: function() {
                            t.dispatch({
                                type: "MERGE_EVENTS",
                                eventStore: s
                            })
                        }
                    };
                    t.emitter.trigger("eventResize", r(r({}, c), {
                        el: n.draggingSegEl,
                        startDelta: n.validMutation.startDelta || qt(0),
                        endDelta: n.validMutation.endDelta || qt(0),
                        jsEvent: e.origEvent,
                        view: t.viewApi
                    })),
                    t.emitter.trigger("eventChange", c)
                } else
                    t.emitter.trigger("_noEventResize");
                n.draggingSeg = null,
                n.relevantEvents = null,
                n.validMutation = null
            }
            ;
            var o = t.component
              , i = n.dragging = new nl(t.el);
            i.pointer.selector = ".fc-event-resizer",
            i.touchScrollAllowed = !1,
            i.autoScroller.isEnabled = o.context.options.dragScroll;
            var a = n.hitDragging = new ol(n.dragging,Da(t));
            return a.emitter.on("pointerdown", n.handlePointerDown),
            a.emitter.on("dragstart", n.handleDragStart),
            a.emitter.on("hitupdate", n.handleHitUpdate),
            a.emitter.on("dragend", n.handleDragEnd),
            n
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        t.prototype.querySegEl = function(e) {
            return Pe(e.subjectEl, ".fc-event")
        }
        ,
        t
    }(ba);
    var dl = function() {
        function e(e) {
            var t = this;
            this.context = e,
            this.isRecentPointerDateSelect = !1,
            this.matchesCancel = !1,
            this.matchesEvent = !1,
            this.onSelect = function(e) {
                e.jsEvent && (t.isRecentPointerDateSelect = !0)
            }
            ,
            this.onDocumentPointerDown = function(e) {
                var n = t.context.options.unselectCancel
                  , r = Ue(e.origEvent);
                t.matchesCancel = !!Pe(r, n),
                t.matchesEvent = !!Pe(r, ul.SELECTOR)
            }
            ,
            this.onDocumentPointerUp = function(e) {
                var n = t.context
                  , r = t.documentPointer
                  , o = n.getCurrentData();
                if (!r.wasTouchScroll) {
                    if (o.dateSelection && !t.isRecentPointerDateSelect) {
                        var i = n.options.unselectAuto;
                        !i || i && t.matchesCancel || n.calendarApi.unselect(e)
                    }
                    o.eventSelection && !t.matchesEvent && n.dispatch({
                        type: "UNSELECT_EVENT"
                    })
                }
                t.isRecentPointerDateSelect = !1
            }
            ;
            var n = this.documentPointer = new Zs(document);
            n.shouldIgnoreMove = !0,
            n.shouldWatchScroll = !1,
            n.emitter.on("pointerdown", this.onDocumentPointerDown),
            n.emitter.on("pointerup", this.onDocumentPointerUp),
            e.emitter.on("select", this.onSelect)
        }
        return e.prototype.destroy = function() {
            this.context.emitter.off("select", this.onSelect),
            this.documentPointer.destroy()
        }
        ,
        e
    }()
      , pl = {
        fixedMirrorParent: Wn
    }
      , fl = {
        dateClick: Wn,
        eventDragStart: Wn,
        eventDragStop: Wn,
        eventDrop: Wn,
        eventResizeStart: Wn,
        eventResizeStop: Wn,
        eventResize: Wn,
        drop: Wn,
        eventReceive: Wn,
        eventLeave: Wn
    }
      , hl = function() {
        function e(e, t) {
            var n = this;
            this.receivingContext = null,
            this.droppableEvent = null,
            this.suppliedDragMeta = null,
            this.dragMeta = null,
            this.handleDragStart = function(e) {
                n.dragMeta = n.buildDragMeta(e.subjectEl)
            }
            ,
            this.handleHitUpdate = function(e, t, o) {
                var i = n.hitDragging.dragging
                  , a = null
                  , s = null
                  , l = !1
                  , u = {
                    affectedEvents: {
                        defs: {},
                        instances: {}
                    },
                    mutatedEvents: {
                        defs: {},
                        instances: {}
                    },
                    isEvent: n.dragMeta.create
                };
                e && (a = e.context,
                n.canDropElOnCalendar(o.subjectEl, a) && (s = function(e, t, n) {
                    for (var o = r({}, t.leftoverProps), i = 0, a = n.pluginHooks.externalDefTransforms; i < a.length; i++) {
                        var s = a[i];
                        r(o, s(e, t))
                    }
                    var l = er(o, n)
                      , u = l.refined
                      , c = l.extra
                      , d = nr(u, c, t.sourceId, e.allDay, n.options.forceEventDuration || Boolean(t.duration), n)
                      , p = e.range.start;
                    e.allDay && t.startTime && (p = n.dateEnv.add(p, t.startTime));
                    var f = t.duration ? n.dateEnv.add(p, t.duration) : Lr(e.allDay, p, n)
                      , h = Mt(d.defId, {
                        start: p,
                        end: f
                    });
                    return {
                        def: d,
                        instance: h
                    }
                }(e.dateSpan, n.dragMeta, a),
                u.mutatedEvents = Un(s),
                (l = !Ja(u, e.dateProfile, a)) && (u.mutatedEvents = {
                    defs: {},
                    instances: {}
                },
                s = null))),
                n.displayDrag(a, u),
                i.setMirrorIsVisible(t || !s || !document.querySelector(".fc-event-mirror")),
                l ? $e() : Je(),
                t || (i.setMirrorNeedsRevert(!s),
                n.receivingContext = a,
                n.droppableEvent = s)
            }
            ,
            this.handleDragEnd = function(e) {
                var t = n
                  , o = t.receivingContext
                  , i = t.droppableEvent;
                if (n.clearDrag(),
                o && i) {
                    var a = n.hitDragging.finalHit
                      , s = a.context.viewApi
                      , l = n.dragMeta;
                    if (o.emitter.trigger("drop", r(r({}, al(a.dateSpan, o)), {
                        draggedEl: e.subjectEl,
                        jsEvent: e.origEvent,
                        view: s
                    })),
                    l.create) {
                        var u = Un(i);
                        o.dispatch({
                            type: "MERGE_EVENTS",
                            eventStore: u
                        }),
                        e.isTouch && o.dispatch({
                            type: "SELECT_EVENT",
                            eventInstanceId: i.instance.instanceId
                        }),
                        o.emitter.trigger("eventReceive", {
                            event: new Zr(o,i.def,i.instance),
                            relatedEvents: [],
                            revert: function() {
                                o.dispatch({
                                    type: "REMOVE_EVENTS",
                                    eventStore: u
                                })
                            },
                            draggedEl: e.subjectEl,
                            view: s
                        })
                    }
                }
                n.receivingContext = null,
                n.droppableEvent = null
            }
            ;
            var o = this.hitDragging = new ol(e,Ra);
            o.requireInitial = !1,
            o.emitter.on("dragstart", this.handleDragStart),
            o.emitter.on("hitupdate", this.handleHitUpdate),
            o.emitter.on("dragend", this.handleDragEnd),
            this.suppliedDragMeta = t
        }
        return e.prototype.buildDragMeta = function(e) {
            return "object" == typeof this.suppliedDragMeta ? xa(this.suppliedDragMeta) : "function" == typeof this.suppliedDragMeta ? xa(this.suppliedDragMeta(e)) : xa((t = function(e, t) {
                var n = Ta.dataAttrPrefix
                  , r = (n ? n + "-" : "") + t;
                return e.getAttribute("data-" + r) || ""
            }(e, "event")) ? JSON.parse(t) : {
                create: !1
            });
            var t
        }
        ,
        e.prototype.displayDrag = function(e, t) {
            var n = this.receivingContext;
            n && n !== e && n.dispatch({
                type: "UNSET_EVENT_DRAG"
            }),
            e && e.dispatch({
                type: "SET_EVENT_DRAG",
                state: t
            })
        }
        ,
        e.prototype.clearDrag = function() {
            this.receivingContext && this.receivingContext.dispatch({
                type: "UNSET_EVENT_DRAG"
            })
        }
        ,
        e.prototype.canDropElOnCalendar = function(e, t) {
            var n = t.options.dropAccept;
            return "function" == typeof n ? n.call(t.calendarApi, e) : "string" != typeof n || !n || Boolean(Ne(e, n))
        }
        ,
        e
    }();
    Ta.dataAttrPrefix = "";
    var vl = function() {
        function e(e, t) {
            var n = this;
            void 0 === t && (t = {}),
            this.handlePointerDown = function(e) {
                var t = n.dragging
                  , r = n.settings
                  , o = r.minDistance
                  , i = r.longPressDelay;
                t.minDistance = null != o ? o : e.isTouch ? 0 : kn.eventDragMinDistance,
                t.delay = e.isTouch ? null != i ? i : kn.longPressDelay : 0
            }
            ,
            this.handleDragStart = function(e) {
                e.isTouch && n.dragging.delay && e.subjectEl.classList.contains("fc-event") && n.dragging.mirror.getMirrorEl().classList.add("fc-event-selected")
            }
            ,
            this.settings = t;
            var r = this.dragging = new nl(e);
            r.touchScrollAllowed = !1,
            null != t.itemSelector && (r.pointer.selector = t.itemSelector),
            null != t.appendTo && (r.mirror.parentNode = t.appendTo),
            r.emitter.on("pointerdown", this.handlePointerDown),
            r.emitter.on("dragstart", this.handleDragStart),
            new hl(r,t.eventData)
        }
        return e.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        e
    }()
      , gl = function(e) {
        function t(t) {
            var n = e.call(this, t) || this;
            n.shouldIgnoreMove = !1,
            n.mirrorSelector = "",
            n.currentMirrorEl = null,
            n.handlePointerDown = function(e) {
                n.emitter.trigger("pointerdown", e),
                n.shouldIgnoreMove || n.emitter.trigger("dragstart", e)
            }
            ,
            n.handlePointerMove = function(e) {
                n.shouldIgnoreMove || n.emitter.trigger("dragmove", e)
            }
            ,
            n.handlePointerUp = function(e) {
                n.emitter.trigger("pointerup", e),
                n.shouldIgnoreMove || n.emitter.trigger("dragend", e)
            }
            ;
            var r = n.pointer = new Zs(t);
            return r.emitter.on("pointerdown", n.handlePointerDown),
            r.emitter.on("pointermove", n.handlePointerMove),
            r.emitter.on("pointerup", n.handlePointerUp),
            n
        }
        return n(t, e),
        t.prototype.destroy = function() {
            this.pointer.destroy()
        }
        ,
        t.prototype.setIgnoreMove = function(e) {
            this.shouldIgnoreMove = e
        }
        ,
        t.prototype.setMirrorIsVisible = function(e) {
            if (e)
                this.currentMirrorEl && (this.currentMirrorEl.style.visibility = "",
                this.currentMirrorEl = null);
            else {
                var t = this.mirrorSelector ? document.querySelector(this.mirrorSelector) : null;
                t && (this.currentMirrorEl = t,
                t.style.visibility = "hidden")
            }
        }
        ,
        t
    }(wa)
      , ml = function() {
        function e(e, t) {
            var n = document;
            e === document || e instanceof Element ? (n = e,
            t = t || {}) : t = e || {};
            var r = this.dragging = new gl(n);
            "string" == typeof t.itemSelector ? r.pointer.selector = t.itemSelector : n === document && (r.pointer.selector = "[data-event]"),
            "string" == typeof t.mirrorSelector && (r.mirrorSelector = t.mirrorSelector),
            new hl(r,t.eventData)
        }
        return e.prototype.destroy = function() {
            this.dragging.destroy()
        }
        ,
        e
    }()
      , yl = ci({
        componentInteractions: [sl, ll, ul, cl],
        calendarInteractions: [dl],
        elementDraggingImpl: nl,
        optionRefiners: pl,
        listenerRefiners: fl
    })
      , Sl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.headerElRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.renderSimpleLayout = function(e, t) {
            var n = this.props
              , r = this.context
              , o = []
              , i = Es(r.options);
            return e && o.push({
                type: "header",
                key: "header",
                isSticky: i,
                chunk: {
                    elRef: this.headerElRef,
                    tableClassName: "fc-col-header",
                    rowContent: e
                }
            }),
            o.push({
                type: "body",
                key: "body",
                liquid: !0,
                chunk: {
                    content: t
                }
            }),
            Yo(Ci, {
                viewSpec: r.viewSpec
            }, (function(e, t) {
                return Yo("div", {
                    ref: e,
                    className: ["fc-daygrid"].concat(t).join(" ")
                }, Yo(Cs, {
                    liquid: !n.isHeightAuto && !n.forPrint,
                    collapsibleWidth: n.forPrint,
                    cols: [],
                    sections: o
                }))
            }
            ))
        }
        ,
        t.prototype.renderHScrollLayout = function(e, t, n, r) {
            var o = this.context.pluginHooks.scrollGridImpl;
            if (!o)
                throw new Error("No ScrollGrid implementation");
            var i = this.props
              , a = this.context
              , s = !i.forPrint && Es(a.options)
              , l = !i.forPrint && bs(a.options)
              , u = [];
            return e && u.push({
                type: "header",
                key: "header",
                isSticky: s,
                chunks: [{
                    key: "main",
                    elRef: this.headerElRef,
                    tableClassName: "fc-col-header",
                    rowContent: e
                }]
            }),
            u.push({
                type: "body",
                key: "body",
                liquid: !0,
                chunks: [{
                    key: "main",
                    content: t
                }]
            }),
            l && u.push({
                type: "footer",
                key: "footer",
                isSticky: !0,
                chunks: [{
                    key: "main",
                    content: Ss
                }]
            }),
            Yo(Ci, {
                viewSpec: a.viewSpec
            }, (function(e, t) {
                return Yo("div", {
                    ref: e,
                    className: ["fc-daygrid"].concat(t).join(" ")
                }, Yo(o, {
                    liquid: !i.isHeightAuto && !i.forPrint,
                    collapsibleWidth: i.forPrint,
                    colGroups: [{
                        cols: [{
                            span: n,
                            minWidth: r
                        }]
                    }],
                    sections: u
                }))
            }
            ))
        }
        ,
        t
    }(ui);
    function El(e, t) {
        for (var n = [], r = 0; r < t; r += 1)
            n[r] = [];
        for (var o = 0, i = e; o < i.length; o++) {
            var a = i[o];
            n[a.row].push(a)
        }
        return n
    }
    function bl(e, t) {
        for (var n = [], r = 0; r < t; r += 1)
            n[r] = [];
        for (var o = 0, i = e; o < i.length; o++) {
            var a = i[o];
            n[a.firstCol].push(a)
        }
        return n
    }
    function Cl(e, t) {
        var n = [];
        if (e) {
            for (a = 0; a < t; a += 1)
                n[a] = {
                    affectedInstances: e.affectedInstances,
                    isEvent: e.isEvent,
                    segs: []
                };
            for (var r = 0, o = e.segs; r < o.length; r++) {
                var i = o[r];
                n[i.row].segs.push(i)
            }
        } else
            for (var a = 0; a < t; a += 1)
                n[a] = null;
        return n
    }
    var Dl = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = ko(this.context, e.date);
            return Yo(xs, {
                date: e.date,
                dateProfile: e.dateProfile,
                todayRange: e.todayRange,
                showDayNumber: e.showDayNumber,
                extraHookProps: e.extraHookProps,
                defaultContent: Rl
            }, (function(n, o) {
                return (o || e.forceDayTop) && Yo("div", {
                    className: "fc-daygrid-day-top",
                    ref: n
                }, Yo("a", r({
                    id: e.dayNumberId,
                    className: "fc-daygrid-day-number"
                }, t), o || Yo(Ko, null, " ")))
            }
            ))
        }
        ,
        t
    }(ii);
    function Rl(e) {
        return e.dayNumberText
    }
    var wl = _n({
        hour: "numeric",
        minute: "2-digit",
        omitZeroMinute: !0,
        meridiem: "narrow"
    });
    function Tl(e) {
        var t = e.eventRange.ui.display;
        return "list-item" === t || "auto" === t && !e.eventRange.def.allDay && e.firstCol === e.lastCol && e.isStart && e.isEnd
    }
    var _l = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props;
            return Yo(Rs, r({}, e, {
                extraClassNames: ["fc-daygrid-event", "fc-daygrid-block-event", "fc-h-event"],
                defaultTimeFormat: wl,
                defaultDisplayEventEnd: e.defaultDisplayEventEnd,
                disableResizing: !e.seg.eventRange.def.allDay
            }))
        }
        ,
        t
    }(ii)
      , xl = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options.eventTimeFormat || wl
              , o = wr(e.seg, n, t, !0, e.defaultDisplayEventEnd);
            return Yo(Ds, {
                seg: e.seg,
                timeText: o,
                defaultContent: kl,
                isDragging: e.isDragging,
                isResizing: !1,
                isDateSelecting: !1,
                isSelected: e.isSelected,
                isPast: e.isPast,
                isFuture: e.isFuture,
                isToday: e.isToday
            }, (function(n, o, i, a) {
                return Yo("a", r({
                    className: ["fc-daygrid-event", "fc-daygrid-dot-event"].concat(o).join(" "),
                    ref: n
                }, kr(e.seg, t)), a)
            }
            ))
        }
        ,
        t
    }(ii);
    function kl(e) {
        return Yo(Ko, null, Yo("div", {
            className: "fc-daygrid-event-dot",
            style: {
                borderColor: e.borderColor || e.backgroundColor
            }
        }), e.timeText && Yo("div", {
            className: "fc-event-time"
        }, e.timeText), Yo("div", {
            className: "fc-event-title"
        }, e.event.title || Yo(Ko, null, " ")))
    }
    var Ml = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.compileSegs = cn(Il),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.compileSegs(e.singlePlacements)
              , n = t.allSegs
              , o = t.invisibleSegs;
            return Yo(Ls, {
                dateProfile: e.dateProfile,
                todayRange: e.todayRange,
                allDayDate: e.allDayDate,
                moreCnt: e.moreCnt,
                allSegs: n,
                hiddenSegs: o,
                alignmentElRef: e.alignmentElRef,
                alignGridTop: e.alignGridTop,
                extraDateSpan: e.extraDateSpan,
                popoverContent: function() {
                    var t = (e.eventDrag ? e.eventDrag.affectedInstances : null) || (e.eventResize ? e.eventResize.affectedInstances : null) || {};
                    return Yo(Ko, null, n.map((function(n) {
                        var o = n.eventRange.instance.instanceId;
                        return Yo("div", {
                            className: "fc-daygrid-event-harness",
                            key: o,
                            style: {
                                visibility: t[o] ? "hidden" : ""
                            }
                        }, Tl(n) ? Yo(xl, r({
                            seg: n,
                            isDragging: !1,
                            isSelected: o === e.eventSelection,
                            defaultDisplayEventEnd: !1
                        }, Tr(n, e.todayRange))) : Yo(_l, r({
                            seg: n,
                            isDragging: !1,
                            isResizing: !1,
                            isDateSelecting: !1,
                            isSelected: o === e.eventSelection,
                            defaultDisplayEventEnd: !1
                        }, Tr(n, e.todayRange))))
                    }
                    )))
                }
            }, (function(e, t, n, o, i, a, s, l) {
                return Yo("a", r({
                    ref: e,
                    className: ["fc-daygrid-more-link"].concat(t).join(" "),
                    title: a,
                    "aria-expanded": s,
                    "aria-controls": l
                }, Ye(i)), o)
            }
            ))
        }
        ,
        t
    }(ii);
    function Il(e) {
        for (var t = [], n = [], r = 0, o = e; r < o.length; r++) {
            var i = o[r];
            t.push(i.seg),
            i.isVisible || n.push(i.seg)
        }
        return {
            allSegs: t,
            invisibleSegs: n
        }
    }
    var Pl = _n({
        week: "narrow"
    })
      , Nl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t.state = {
                dayNumberId: Ve()
            },
            t.handleRootEl = function(e) {
                li(t.rootElRef, e),
                li(t.props.elRef, e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.context
              , n = e.props
              , o = e.state
              , i = e.rootElRef
              , a = n.date
              , s = n.dateProfile
              , l = ko(t, a, "week");
            return Yo(Ms, {
                date: a,
                dateProfile: s,
                todayRange: n.todayRange,
                showDayNumber: n.showDayNumber,
                extraHookProps: n.extraHookProps,
                elRef: this.handleRootEl
            }, (function(e, t, u, c) {
                return Yo("td", r({
                    ref: e,
                    role: "gridcell",
                    className: ["fc-daygrid-day"].concat(t, n.extraClassNames || []).join(" ")
                }, u, n.extraDataAttrs, n.showDayNumber ? {
                    "aria-labelledby": o.dayNumberId
                } : {}), Yo("div", {
                    className: "fc-daygrid-day-frame fc-scrollgrid-sync-inner",
                    ref: n.innerElRef
                }, n.showWeekNumber && Yo(Hs, {
                    date: a,
                    defaultFormat: Pl
                }, (function(e, t, n, o) {
                    return Yo("a", r({
                        ref: e,
                        className: ["fc-daygrid-week-number"].concat(t).join(" ")
                    }, l), o)
                }
                )), !c && Yo(Dl, {
                    date: a,
                    dateProfile: s,
                    showDayNumber: n.showDayNumber,
                    dayNumberId: o.dayNumberId,
                    forceDayTop: n.forceDayTop,
                    todayRange: n.todayRange,
                    extraHookProps: n.extraHookProps
                }), Yo("div", {
                    className: "fc-daygrid-day-events",
                    ref: n.fgContentElRef
                }, n.fgContent, Yo("div", {
                    className: "fc-daygrid-day-bottom",
                    style: {
                        marginTop: n.moreMarginTop
                    }
                }, Yo(Ml, {
                    allDayDate: a,
                    singlePlacements: n.singlePlacements,
                    moreCnt: n.moreCnt,
                    alignmentElRef: i,
                    alignGridTop: !n.showDayNumber,
                    extraDateSpan: n.extraDateSpan,
                    dateProfile: n.dateProfile,
                    eventSelection: n.eventSelection,
                    eventDrag: n.eventDrag,
                    eventResize: n.eventResize,
                    todayRange: n.todayRange
                }))), Yo("div", {
                    className: "fc-daygrid-day-bg"
                }, n.bgContent)))
            }
            ))
        }
        ,
        t
    }(ui);
    function Hl(e, t, n, r, o, i, a) {
        var s = new Al;
        s.allowReslicing = !0,
        s.strictOrder = r,
        !0 === t || !0 === n ? (s.maxCoord = i,
        s.hiddenConsumes = !0) : "number" == typeof t ? s.maxStackCnt = t : "number" == typeof n && (s.maxStackCnt = n,
        s.hiddenConsumes = !0);
        for (var l = [], u = [], c = 0; c < e.length; c += 1) {
            var d = o[(w = e[c]).eventRange.instance.instanceId];
            null != d ? l.push({
                index: c,
                thickness: d,
                span: {
                    start: w.firstCol,
                    end: w.lastCol + 1
                }
            }) : u.push(w)
        }
        for (var p = s.addSegs(l), f = function(e, t, n) {
            for (var r = function(e, t) {
                for (var n = [], r = 0; r < t; r += 1)
                    n.push([]);
                for (var o = 0, i = e; o < i.length; o++) {
                    var a = i[o];
                    for (r = a.span.start; r < a.span.end; r += 1)
                        n[r].push(a)
                }
                return n
            }(e, n.length), o = [], i = [], a = [], s = 0; s < n.length; s += 1) {
                for (var l = r[s], u = [], c = 0, d = 0, p = 0, f = l; p < f.length; p++) {
                    var h = t[(y = f[p]).index];
                    u.push({
                        seg: Ol(h, s, s + 1, n),
                        isVisible: !0,
                        isAbsolute: !1,
                        absoluteTop: y.levelCoord,
                        marginTop: y.levelCoord - c
                    }),
                    c = y.levelCoord + y.thickness
                }
                var v = [];
                c = 0,
                d = 0;
                for (var g = 0, m = l; g < m.length; g++) {
                    h = t[(y = m[g]).index];
                    var y, S = y.span.end - y.span.start > 1, E = y.span.start === s;
                    d += y.levelCoord - c,
                    c = y.levelCoord + y.thickness,
                    S ? (d += y.thickness,
                    E && v.push({
                        seg: Ol(h, y.span.start, y.span.end, n),
                        isVisible: !0,
                        isAbsolute: !0,
                        absoluteTop: y.levelCoord,
                        marginTop: 0
                    })) : E && (v.push({
                        seg: Ol(h, y.span.start, y.span.end, n),
                        isVisible: !0,
                        isAbsolute: !1,
                        absoluteTop: y.levelCoord,
                        marginTop: d
                    }),
                    d = 0)
                }
                o.push(u),
                i.push(v),
                a.push(d)
            }
            return {
                singleColPlacements: o,
                multiColPlacements: i,
                leftoverMargins: a
            }
        }(s.toRects(), e, a), h = f.singleColPlacements, v = f.multiColPlacements, g = f.leftoverMargins, m = [], y = [], S = 0, E = u; S < E.length; S++) {
            v[(w = E[S]).firstCol].push({
                seg: w,
                isVisible: !1,
                isAbsolute: !0,
                absoluteTop: 0,
                marginTop: 0
            });
            for (var b = w.firstCol; b <= w.lastCol; b += 1)
                h[b].push({
                    seg: Ol(w, b, b + 1, a),
                    isVisible: !1,
                    isAbsolute: !1,
                    absoluteTop: 0,
                    marginTop: 0
                })
        }
        for (b = 0; b < a.length; b += 1)
            m.push(0);
        for (var C = 0, D = p; C < D.length; C++) {
            var R = D[C]
              , w = e[R.index]
              , T = R.span;
            v[T.start].push({
                seg: Ol(w, T.start, T.end, a),
                isVisible: !1,
                isAbsolute: !0,
                absoluteTop: 0,
                marginTop: 0
            });
            for (b = T.start; b < T.end; b += 1)
                m[b] += 1,
                h[b].push({
                    seg: Ol(w, b, b + 1, a),
                    isVisible: !1,
                    isAbsolute: !1,
                    absoluteTop: 0,
                    marginTop: 0
                })
        }
        for (b = 0; b < a.length; b += 1)
            y.push(g[b]);
        return {
            singleColPlacements: h,
            multiColPlacements: v,
            moreCnts: m,
            moreMarginTops: y
        }
    }
    function Ol(e, t, n, o) {
        if (e.firstCol === t && e.lastCol === n - 1)
            return e;
        var i = e.eventRange
          , a = i.range
          , s = ur(a, {
            start: o[t].date,
            end: ht(o[n - 1].date, 1)
        });
        return r(r({}, e), {
            firstCol: t,
            lastCol: n - 1,
            eventRange: {
                def: i.def,
                ui: r(r({}, i.ui), {
                    durationEditable: !1
                }),
                instance: i.instance,
                range: s
            },
            isStart: e.isStart && s.start.valueOf() === a.start.valueOf(),
            isEnd: e.isEnd && s.end.valueOf() === a.end.valueOf()
        })
    }
    var Al = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.hiddenConsumes = !1,
            t.forceHidden = {},
            t
        }
        return n(t, e),
        t.prototype.addSegs = function(t) {
            for (var n = this, r = e.prototype.addSegs.call(this, t), o = this.entriesByLevel, i = function(e) {
                return !n.forceHidden[va(e)]
            }, a = 0; a < o.length; a += 1)
                o[a] = o[a].filter(i);
            return r
        }
        ,
        t.prototype.handleInvalidInsertion = function(t, n, o) {
            var i = this.entriesByLevel
              , a = this.forceHidden
              , s = t.touchingEntry
              , l = t.touchingLevel
              , u = t.touchingLateral;
            if (this.hiddenConsumes && s) {
                var c = va(s);
                if (!a[c])
                    if (this.allowReslicing) {
                        var d = r(r({}, s), {
                            span: ya(s.span, n.span)
                        });
                        a[va(d)] = !0,
                        i[l][u] = d,
                        this.splitEntry(s, n, o)
                    } else
                        a[c] = !0,
                        o.push(s)
            }
            return e.prototype.handleInvalidInsertion.call(this, t, n, o)
        }
        ,
        t
    }(fa)
      , Wl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.cellElRefs = new ls,
            t.frameElRefs = new ls,
            t.fgElRefs = new ls,
            t.segHarnessRefs = new ls,
            t.rootElRef = Xo(),
            t.state = {
                framePositions: null,
                maxContentHeight: null,
                eventInstanceHeights: {}
            },
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this
              , n = t.props
              , r = t.state
              , o = t.context.options
              , i = n.cells.length
              , a = bl(n.businessHourSegs, i)
              , s = bl(n.bgEventSegs, i)
              , l = bl(this.getHighlightSegs(), i)
              , u = bl(this.getMirrorSegs(), i)
              , c = Hl(Er(n.fgEventSegs, o.eventOrder), n.dayMaxEvents, n.dayMaxEventRows, o.eventOrderStrict, r.eventInstanceHeights, r.maxContentHeight, n.cells)
              , d = c.singleColPlacements
              , p = c.multiColPlacements
              , f = c.moreCnts
              , h = c.moreMarginTops
              , v = n.eventDrag && n.eventDrag.affectedInstances || n.eventResize && n.eventResize.affectedInstances || {};
            return Yo("tr", {
                ref: this.rootElRef,
                role: "row"
            }, n.renderIntro && n.renderIntro(), n.cells.map((function(t, r) {
                var o = e.renderFgSegs(r, n.forPrint ? d[r] : p[r], n.todayRange, v)
                  , i = e.renderFgSegs(r, function(e, t) {
                    if (!e.length)
                        return [];
                    var n = function(e) {
                        for (var t = {}, n = 0, r = e; n < r.length; n++)
                            for (var o = 0, i = r[n]; o < i.length; o++) {
                                var a = i[o];
                                t[a.seg.eventRange.instance.instanceId] = a.absoluteTop
                            }
                        return t
                    }(t);
                    return e.map((function(e) {
                        return {
                            seg: e,
                            isVisible: !0,
                            isAbsolute: !0,
                            absoluteTop: n[e.eventRange.instance.instanceId],
                            marginTop: 0
                        }
                    }
                    ))
                }(u[r], p), n.todayRange, {}, Boolean(n.eventDrag), Boolean(n.eventResize), !1);
                return Yo(Nl, {
                    key: t.key,
                    elRef: e.cellElRefs.createRef(t.key),
                    innerElRef: e.frameElRefs.createRef(t.key),
                    dateProfile: n.dateProfile,
                    date: t.date,
                    showDayNumber: n.showDayNumbers,
                    showWeekNumber: n.showWeekNumbers && 0 === r,
                    forceDayTop: n.showWeekNumbers,
                    todayRange: n.todayRange,
                    eventSelection: n.eventSelection,
                    eventDrag: n.eventDrag,
                    eventResize: n.eventResize,
                    extraHookProps: t.extraHookProps,
                    extraDataAttrs: t.extraDataAttrs,
                    extraClassNames: t.extraClassNames,
                    extraDateSpan: t.extraDateSpan,
                    moreCnt: f[r],
                    moreMarginTop: h[r],
                    singlePlacements: d[r],
                    fgContentElRef: e.fgElRefs.createRef(t.key),
                    fgContent: Yo(Ko, null, Yo(Ko, null, o), Yo(Ko, null, i)),
                    bgContent: Yo(Ko, null, e.renderFillSegs(l[r], "highlight"), e.renderFillSegs(a[r], "non-business"), e.renderFillSegs(s[r], "bg-event"))
                })
            }
            )))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateSizing(!0)
        }
        ,
        t.prototype.componentDidUpdate = function(e, t) {
            var n = this.props;
            this.updateSizing(!Wt(e, n))
        }
        ,
        t.prototype.getHighlightSegs = function() {
            var e = this.props;
            return e.eventDrag && e.eventDrag.segs.length ? e.eventDrag.segs : e.eventResize && e.eventResize.segs.length ? e.eventResize.segs : e.dateSelectionSegs
        }
        ,
        t.prototype.getMirrorSegs = function() {
            var e = this.props;
            return e.eventResize && e.eventResize.segs.length ? e.eventResize.segs : []
        }
        ,
        t.prototype.renderFgSegs = function(e, t, n, o, i, a, s) {
            var l = this.context
              , u = this.props.eventSelection
              , c = this.state.framePositions
              , d = 1 === this.props.cells.length
              , p = i || a || s
              , f = [];
            if (c)
                for (var h = 0, v = t; h < v.length; h++) {
                    var g = v[h]
                      , m = g.seg
                      , y = m.eventRange.instance.instanceId
                      , S = y + ":" + e
                      , E = g.isVisible && !o[y]
                      , b = g.isAbsolute
                      , C = ""
                      , D = "";
                    b && (l.isRtl ? (D = 0,
                    C = c.lefts[m.lastCol] - c.lefts[m.firstCol]) : (C = 0,
                    D = c.rights[m.firstCol] - c.rights[m.lastCol])),
                    f.push(Yo("div", {
                        className: "fc-daygrid-event-harness" + (b ? " fc-daygrid-event-harness-abs" : ""),
                        key: S,
                        ref: p ? null : this.segHarnessRefs.createRef(S),
                        style: {
                            visibility: E ? "" : "hidden",
                            marginTop: b ? "" : g.marginTop,
                            top: b ? g.absoluteTop : "",
                            left: C,
                            right: D
                        }
                    }, Tl(m) ? Yo(xl, r({
                        seg: m,
                        isDragging: i,
                        isSelected: y === u,
                        defaultDisplayEventEnd: d
                    }, Tr(m, n))) : Yo(_l, r({
                        seg: m,
                        isDragging: i,
                        isResizing: a,
                        isDateSelecting: s,
                        isSelected: y === u,
                        defaultDisplayEventEnd: d
                    }, Tr(m, n)))))
                }
            return f
        }
        ,
        t.prototype.renderFillSegs = function(e, t) {
            var n = this.context.isRtl
              , i = this.props.todayRange
              , a = this.state.framePositions
              , s = [];
            if (a)
                for (var l = 0, u = e; l < u.length; l++) {
                    var c = u[l]
                      , d = n ? {
                        right: 0,
                        left: a.lefts[c.lastCol] - a.lefts[c.firstCol]
                    } : {
                        left: 0,
                        right: a.rights[c.firstCol] - a.rights[c.lastCol]
                    };
                    s.push(Yo("div", {
                        key: xr(c.eventRange),
                        className: "fc-daygrid-bg-harness",
                        style: d
                    }, "bg-event" === t ? Yo(Ps, r({
                        seg: c
                    }, Tr(c, i))) : Is(t)))
                }
            return Yo.apply(void 0, o([Ko, {}], s))
        }
        ,
        t.prototype.updateSizing = function(e) {
            var t = this.props
              , n = this.frameElRefs;
            if (!t.forPrint && null !== t.clientWidth) {
                if (e) {
                    var o = t.cells.map((function(e) {
                        return n.currentMap[e.key]
                    }
                    ));
                    if (o.length) {
                        var i = this.rootElRef.current;
                        this.setState({
                            framePositions: new zo(i,o,!0,!1)
                        })
                    }
                }
                var a = this.state.eventInstanceHeights
                  , s = this.queryEventInstanceHeights()
                  , l = !0 === t.dayMaxEvents || !0 === t.dayMaxEventRows;
                this.safeSetState({
                    eventInstanceHeights: r(r({}, a), s),
                    maxContentHeight: l ? this.computeMaxContentHeight() : null
                })
            }
        }
        ,
        t.prototype.queryEventInstanceHeights = function() {
            var e = this.segHarnessRefs.currentMap
              , t = {};
            for (var n in e) {
                var r = Math.round(e[n].getBoundingClientRect().height)
                  , o = n.split(":")[0];
                t[o] = Math.max(t[o] || 0, r)
            }
            return t
        }
        ,
        t.prototype.computeMaxContentHeight = function() {
            var e = this.props.cells[0].key
              , t = this.cellElRefs.currentMap[e]
              , n = this.fgElRefs.currentMap[e];
            return t.getBoundingClientRect().bottom - n.getBoundingClientRect().top
        }
        ,
        t.prototype.getCellEls = function() {
            var e = this.cellElRefs.currentMap;
            return this.props.cells.map((function(t) {
                return e[t.key]
            }
            ))
        }
        ,
        t
    }(ui);
    Wl.addStateEquality({
        eventInstanceHeights: Wt
    });
    var Ll = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.splitBusinessHourSegs = cn(El),
            t.splitBgEventSegs = cn(El),
            t.splitFgEventSegs = cn(El),
            t.splitDateSelectionSegs = cn(El),
            t.splitEventDrag = cn(Cl),
            t.splitEventResize = cn(Cl),
            t.rowRefs = new ls,
            t.handleRootEl = function(e) {
                t.rootEl = e,
                e ? t.context.registerInteractiveComponent(t, {
                    el: e,
                    isHitComboAllowed: t.props.isHitComboAllowed
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = t.dateProfile
              , r = t.dayMaxEventRows
              , o = t.dayMaxEvents
              , i = t.expandRows
              , a = t.cells.length
              , s = this.splitBusinessHourSegs(t.businessHourSegs, a)
              , l = this.splitBgEventSegs(t.bgEventSegs, a)
              , u = this.splitFgEventSegs(t.fgEventSegs, a)
              , c = this.splitDateSelectionSegs(t.dateSelectionSegs, a)
              , d = this.splitEventDrag(t.eventDrag, a)
              , p = this.splitEventResize(t.eventResize, a)
              , f = !0 === o || !0 === r;
            return f && !i && (f = !1,
            r = null,
            o = null),
            Yo("div", {
                className: ["fc-daygrid-body", f ? "fc-daygrid-body-balanced" : "fc-daygrid-body-unbalanced", i ? "" : "fc-daygrid-body-natural"].join(" "),
                ref: this.handleRootEl,
                style: {
                    width: t.clientWidth,
                    minWidth: t.tableMinWidth
                }
            }, Yo(Ga, {
                unit: "day"
            }, (function(f, h) {
                return Yo(Ko, null, Yo("table", {
                    role: "presentation",
                    className: "fc-scrollgrid-sync-table",
                    style: {
                        width: t.clientWidth,
                        minWidth: t.tableMinWidth,
                        height: i ? t.clientHeight : ""
                    }
                }, t.colGroupNode, Yo("tbody", {
                    role: "presentation"
                }, t.cells.map((function(i, f) {
                    return Yo(Wl, {
                        ref: e.rowRefs.createRef(f),
                        key: i.length ? i[0].date.toISOString() : f,
                        showDayNumbers: a > 1,
                        showWeekNumbers: t.showWeekNumbers,
                        todayRange: h,
                        dateProfile: n,
                        cells: i,
                        renderIntro: t.renderRowIntro,
                        businessHourSegs: s[f],
                        eventSelection: t.eventSelection,
                        bgEventSegs: l[f].filter(Ul),
                        fgEventSegs: u[f],
                        dateSelectionSegs: c[f],
                        eventDrag: d[f],
                        eventResize: p[f],
                        dayMaxEvents: o,
                        dayMaxEventRows: r,
                        clientWidth: t.clientWidth,
                        clientHeight: t.clientHeight,
                        forPrint: t.forPrint
                    })
                }
                )))))
            }
            )))
        }
        ,
        t.prototype.prepareHits = function() {
            this.rowPositions = new zo(this.rootEl,this.rowRefs.collect().map((function(e) {
                return e.getCellEls()[0]
            }
            )),!1,!0),
            this.colPositions = new zo(this.rootEl,this.rowRefs.currentMap[0].getCellEls(),!0,!1)
        }
        ,
        t.prototype.queryHit = function(e, t) {
            var n = this.colPositions
              , o = this.rowPositions
              , i = n.leftToIndex(e)
              , a = o.topToIndex(t);
            if (null != a && null != i) {
                var s = this.props.cells[a][i];
                return {
                    dateProfile: this.props.dateProfile,
                    dateSpan: r({
                        range: this.getCellRange(a, i),
                        allDay: !0
                    }, s.extraDateSpan),
                    dayEl: this.getCellEl(a, i),
                    rect: {
                        left: n.lefts[i],
                        right: n.rights[i],
                        top: o.tops[a],
                        bottom: o.bottoms[a]
                    },
                    layer: 0
                }
            }
            return null
        }
        ,
        t.prototype.getCellEl = function(e, t) {
            return this.rowRefs.currentMap[e].getCellEls()[t]
        }
        ,
        t.prototype.getCellRange = function(e, t) {
            var n = this.props.cells[e][t].date;
            return {
                start: n,
                end: ht(n, 1)
            }
        }
        ,
        t
    }(ui);
    function Ul(e) {
        return e.eventRange.def.allDay
    }
    var Bl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.forceDayIfListItem = !0,
            t
        }
        return n(t, e),
        t.prototype.sliceRange = function(e, t) {
            return t.sliceRange(e)
        }
        ,
        t
    }(Ka)
      , zl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.slicer = new Bl,
            t.tableRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context;
            return Yo(Ll, r({
                ref: this.tableRef
            }, this.slicer.sliceProps(e, e.dateProfile, e.nextDayThreshold, t, e.dayTableModel), {
                dateProfile: e.dateProfile,
                cells: e.dayTableModel.cells,
                colGroupNode: e.colGroupNode,
                tableMinWidth: e.tableMinWidth,
                renderRowIntro: e.renderRowIntro,
                dayMaxEvents: e.dayMaxEvents,
                dayMaxEventRows: e.dayMaxEventRows,
                showWeekNumbers: e.showWeekNumbers,
                expandRows: e.expandRows,
                headerAlignElRef: e.headerAlignElRef,
                clientWidth: e.clientWidth,
                clientHeight: e.clientHeight,
                forPrint: e.forPrint
            }))
        }
        ,
        t
    }(ui)
      , Vl = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildDayTableModel = cn(Fl),
            t.headerRef = Xo(),
            t.tableRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.context
              , n = t.options
              , r = t.dateProfileGenerator
              , o = this.props
              , i = this.buildDayTableModel(o.dateProfile, r)
              , a = n.dayHeaders && Yo(qa, {
                ref: this.headerRef,
                dateProfile: o.dateProfile,
                dates: i.headerDates,
                datesRepDistinctDays: 1 === i.rowCnt
            })
              , s = function(t) {
                return Yo(zl, {
                    ref: e.tableRef,
                    dateProfile: o.dateProfile,
                    dayTableModel: i,
                    businessHours: o.businessHours,
                    dateSelection: o.dateSelection,
                    eventStore: o.eventStore,
                    eventUiBases: o.eventUiBases,
                    eventSelection: o.eventSelection,
                    eventDrag: o.eventDrag,
                    eventResize: o.eventResize,
                    nextDayThreshold: n.nextDayThreshold,
                    colGroupNode: t.tableColGroupNode,
                    tableMinWidth: t.tableMinWidth,
                    dayMaxEvents: n.dayMaxEvents,
                    dayMaxEventRows: n.dayMaxEventRows,
                    showWeekNumbers: n.weekNumbers,
                    expandRows: !o.isHeightAuto,
                    headerAlignElRef: e.headerElRef,
                    clientWidth: t.clientWidth,
                    clientHeight: t.clientHeight,
                    forPrint: o.forPrint
                })
            };
            return n.dayMinWidth ? this.renderHScrollLayout(a, s, i.colCnt, n.dayMinWidth) : this.renderSimpleLayout(a, s)
        }
        ,
        t
    }(Sl);
    function Fl(e, t) {
        var n = new Za(e.renderRange,t);
        return new Xa(n,/year|month|week/.test(e.currentRangeUnit))
    }
    var Gl = ci({
        initialView: "dayGridMonth",
        views: {
            dayGrid: {
                component: Vl,
                dateProfileGeneratorClass: function(e) {
                    function t() {
                        return null !== e && e.apply(this, arguments) || this
                    }
                    return n(t, e),
                    t.prototype.buildRenderRange = function(t, n, r) {
                        var o, i = this.props.dateEnv, a = e.prototype.buildRenderRange.call(this, t, n, r), s = a.start, l = a.end;
                        (/^(year|month)$/.test(n) && (s = i.startOfWeek(s),
                        (o = i.startOfWeek(l)).valueOf() !== l.valueOf() && (l = ft(o, 1))),
                        this.props.monthMode && this.props.fixedWeekCount) && (l = ft(l, 6 - Math.ceil(gt(s, l))));
                        return {
                            start: s,
                            end: l
                        }
                    }
                    ,
                    t
                }(_i)
            },
            dayGridDay: {
                type: "dayGrid",
                duration: {
                    days: 1
                }
            },
            dayGridWeek: {
                type: "dayGrid",
                duration: {
                    weeks: 1
                }
            },
            dayGridMonth: {
                type: "dayGrid",
                duration: {
                    months: 1
                },
                monthMode: !0,
                fixedWeekCount: !0
            }
        }
    })
      , jl = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.getKeyInfo = function() {
            return {
                allDay: {},
                timed: {}
            }
        }
        ,
        t.prototype.getKeysForDateSpan = function(e) {
            return e.allDay ? ["allDay"] : ["timed"]
        }
        ,
        t.prototype.getKeysForEventDef = function(e) {
            return e.allDay ? vr(e) ? ["timed", "allDay"] : ["allDay"] : ["timed"]
        }
        ,
        t
    }(Co)
      , ql = _n({
        hour: "numeric",
        minute: "2-digit",
        omitZeroMinute: !0,
        meridiem: "short"
    });
    function Yl(e) {
        var t = ["fc-timegrid-slot", "fc-timegrid-slot-label", e.isLabeled ? "fc-scrollgrid-shrink" : "fc-timegrid-slot-minor"];
        return Yo(ni.Consumer, null, (function(n) {
            if (!e.isLabeled)
                return Yo("td", {
                    className: t.join(" "),
                    "data-time": e.isoTimeStr
                });
            var r = n.dateEnv
              , o = n.options
              , i = n.viewApi
              , a = null == o.slotLabelFormat ? ql : Array.isArray(o.slotLabelFormat) ? _n(o.slotLabelFormat[0]) : _n(o.slotLabelFormat)
              , s = {
                level: 0,
                time: e.time,
                date: r.toDate(e.date),
                view: i,
                text: r.format(e.date, a)
            };
            return Yo(hi, {
                hookProps: s,
                classNames: o.slotLabelClassNames,
                content: o.slotLabelContent,
                defaultContent: Zl,
                didMount: o.slotLabelDidMount,
                willUnmount: o.slotLabelWillUnmount
            }, (function(n, r, o, i) {
                return Yo("td", {
                    ref: n,
                    className: t.concat(r).join(" "),
                    "data-time": e.isoTimeStr
                }, Yo("div", {
                    className: "fc-timegrid-slot-label-frame fc-scrollgrid-shrink-frame"
                }, Yo("div", {
                    className: "fc-timegrid-slot-label-cushion fc-scrollgrid-shrink-cushion",
                    ref: o
                }, i)))
            }
            ))
        }
        ))
    }
    function Zl(e) {
        return e.text
    }
    var Xl = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            return this.props.slatMetas.map((function(e) {
                return Yo("tr", {
                    key: e.key
                }, Yo(Yl, r({}, e)))
            }
            ))
        }
        ,
        t
    }(ii)
      , Kl = _n({
        week: "short"
    })
      , $l = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.allDaySplitter = new jl,
            t.headerElRef = Xo(),
            t.rootElRef = Xo(),
            t.scrollerElRef = Xo(),
            t.state = {
                slatCoords: null
            },
            t.handleScrollTopRequest = function(e) {
                var n = t.scrollerElRef.current;
                n && (n.scrollTop = e)
            }
            ,
            t.renderHeadAxis = function(e, n) {
                void 0 === n && (n = "");
                var o = t.context.options
                  , i = t.props.dateProfile.renderRange
                  , a = 1 === mt(i.start, i.end) ? ko(t.context, i.start, "week") : {};
                return o.weekNumbers && "day" === e ? Yo(Hs, {
                    date: i.start,
                    defaultFormat: Kl
                }, (function(e, t, o, i) {
                    return Yo("th", {
                        ref: e,
                        "aria-hidden": !0,
                        className: ["fc-timegrid-axis", "fc-scrollgrid-shrink"].concat(t).join(" ")
                    }, Yo("div", {
                        className: "fc-timegrid-axis-frame fc-scrollgrid-shrink-frame fc-timegrid-axis-frame-liquid",
                        style: {
                            height: n
                        }
                    }, Yo("a", r({
                        ref: o,
                        className: "fc-timegrid-axis-cushion fc-scrollgrid-shrink-cushion fc-scrollgrid-sync-inner"
                    }, a), i)))
                }
                )) : Yo("th", {
                    "aria-hidden": !0,
                    className: "fc-timegrid-axis"
                }, Yo("div", {
                    className: "fc-timegrid-axis-frame",
                    style: {
                        height: n
                    }
                }))
            }
            ,
            t.renderTableRowAxis = function(e) {
                var n = t.context
                  , r = n.options
                  , o = n.viewApi
                  , i = {
                    text: r.allDayText,
                    view: o
                };
                return Yo(hi, {
                    hookProps: i,
                    classNames: r.allDayClassNames,
                    content: r.allDayContent,
                    defaultContent: Jl,
                    didMount: r.allDayDidMount,
                    willUnmount: r.allDayWillUnmount
                }, (function(t, n, r, o) {
                    return Yo("td", {
                        ref: t,
                        "aria-hidden": !0,
                        className: ["fc-timegrid-axis", "fc-scrollgrid-shrink"].concat(n).join(" ")
                    }, Yo("div", {
                        className: "fc-timegrid-axis-frame fc-scrollgrid-shrink-frame" + (null == e ? " fc-timegrid-axis-frame-liquid" : ""),
                        style: {
                            height: e
                        }
                    }, Yo("span", {
                        className: "fc-timegrid-axis-cushion fc-scrollgrid-shrink-cushion fc-scrollgrid-sync-inner",
                        ref: r
                    }, o)))
                }
                ))
            }
            ,
            t.handleSlatCoords = function(e) {
                t.setState({
                    slatCoords: e
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.renderSimpleLayout = function(e, t, n) {
            var r = this.context
              , o = this.props
              , i = []
              , a = Es(r.options);
            return e && i.push({
                type: "header",
                key: "header",
                isSticky: a,
                chunk: {
                    elRef: this.headerElRef,
                    tableClassName: "fc-col-header",
                    rowContent: e
                }
            }),
            t && (i.push({
                type: "body",
                key: "all-day",
                chunk: {
                    content: t
                }
            }),
            i.push({
                type: "body",
                key: "all-day-divider",
                outerContent: Yo("tr", {
                    role: "presentation",
                    className: "fc-scrollgrid-section"
                }, Yo("td", {
                    className: "fc-timegrid-divider " + r.theme.getClass("tableCellShaded")
                }))
            })),
            i.push({
                type: "body",
                key: "body",
                liquid: !0,
                expandRows: Boolean(r.options.expandRows),
                chunk: {
                    scrollerElRef: this.scrollerElRef,
                    content: n
                }
            }),
            Yo(Ci, {
                viewSpec: r.viewSpec,
                elRef: this.rootElRef
            }, (function(e, t) {
                return Yo("div", {
                    className: ["fc-timegrid"].concat(t).join(" "),
                    ref: e
                }, Yo(Cs, {
                    liquid: !o.isHeightAuto && !o.forPrint,
                    collapsibleWidth: o.forPrint,
                    cols: [{
                        width: "shrink"
                    }],
                    sections: i
                }))
            }
            ))
        }
        ,
        t.prototype.renderHScrollLayout = function(e, t, n, r, o, i, a) {
            var s = this
              , l = this.context.pluginHooks.scrollGridImpl;
            if (!l)
                throw new Error("No ScrollGrid implementation");
            var u = this.context
              , c = this.props
              , d = !c.forPrint && Es(u.options)
              , p = !c.forPrint && bs(u.options)
              , f = [];
            e && f.push({
                type: "header",
                key: "header",
                isSticky: d,
                syncRowHeights: !0,
                chunks: [{
                    key: "axis",
                    rowContent: function(e) {
                        return Yo("tr", {
                            role: "presentation"
                        }, s.renderHeadAxis("day", e.rowSyncHeights[0]))
                    }
                }, {
                    key: "cols",
                    elRef: this.headerElRef,
                    tableClassName: "fc-col-header",
                    rowContent: e
                }]
            }),
            t && (f.push({
                type: "body",
                key: "all-day",
                syncRowHeights: !0,
                chunks: [{
                    key: "axis",
                    rowContent: function(e) {
                        return Yo("tr", {
                            role: "presentation"
                        }, s.renderTableRowAxis(e.rowSyncHeights[0]))
                    }
                }, {
                    key: "cols",
                    content: t
                }]
            }),
            f.push({
                key: "all-day-divider",
                type: "body",
                outerContent: Yo("tr", {
                    role: "presentation",
                    className: "fc-scrollgrid-section"
                }, Yo("td", {
                    colSpan: 2,
                    className: "fc-timegrid-divider " + u.theme.getClass("tableCellShaded")
                }))
            }));
            var h = u.options.nowIndicator;
            return f.push({
                type: "body",
                key: "body",
                liquid: !0,
                expandRows: Boolean(u.options.expandRows),
                chunks: [{
                    key: "axis",
                    content: function(e) {
                        return Yo("div", {
                            className: "fc-timegrid-axis-chunk"
                        }, Yo("table", {
                            "aria-hidden": !0,
                            style: {
                                height: e.expandRows ? e.clientHeight : ""
                            }
                        }, e.tableColGroupNode, Yo("tbody", null, Yo(Xl, {
                            slatMetas: i
                        }))), Yo("div", {
                            className: "fc-timegrid-now-indicator-container"
                        }, Yo(Ga, {
                            unit: h ? "minute" : "day"
                        }, (function(e) {
                            var t = h && a && a.safeComputeTop(e);
                            return "number" == typeof t ? Yo(Ts, {
                                isAxis: !0,
                                date: e
                            }, (function(e, n, r, o) {
                                return Yo("div", {
                                    ref: e,
                                    className: ["fc-timegrid-now-indicator-arrow"].concat(n).join(" "),
                                    style: {
                                        top: t
                                    }
                                }, o)
                            }
                            )) : null
                        }
                        ))))
                    }
                }, {
                    key: "cols",
                    scrollerElRef: this.scrollerElRef,
                    content: n
                }]
            }),
            p && f.push({
                key: "footer",
                type: "footer",
                isSticky: !0,
                chunks: [{
                    key: "axis",
                    content: Ss
                }, {
                    key: "cols",
                    content: Ss
                }]
            }),
            Yo(Ci, {
                viewSpec: u.viewSpec,
                elRef: this.rootElRef
            }, (function(e, t) {
                return Yo("div", {
                    className: ["fc-timegrid"].concat(t).join(" "),
                    ref: e
                }, Yo(l, {
                    liquid: !c.isHeightAuto && !c.forPrint,
                    collapsibleWidth: !1,
                    colGroups: [{
                        width: "shrink",
                        cols: [{
                            width: "shrink"
                        }]
                    }, {
                        cols: [{
                            span: r,
                            minWidth: o
                        }]
                    }],
                    sections: f
                }))
            }
            ))
        }
        ,
        t.prototype.getAllDayMaxEventProps = function() {
            var e = this.context.options
              , t = e.dayMaxEvents
              , n = e.dayMaxEventRows;
            return !0 !== t && !0 !== n || (t = void 0,
            n = 5),
            {
                dayMaxEvents: t,
                dayMaxEventRows: n
            }
        }
        ,
        t
    }(ui);
    function Jl(e) {
        return e.text
    }
    var Ql = function() {
        function e(e, t, n) {
            this.positions = e,
            this.dateProfile = t,
            this.slotDuration = n
        }
        return e.prototype.safeComputeTop = function(e) {
            var t = this.dateProfile;
            if (fr(t.currentRange, e)) {
                var n = bt(e)
                  , r = e.valueOf() - n.valueOf();
                if (r >= en(t.slotMinTime) && r < en(t.slotMaxTime))
                    return this.computeTimeTop(qt(r))
            }
            return null
        }
        ,
        e.prototype.computeDateTop = function(e, t) {
            return t || (t = bt(e)),
            this.computeTimeTop(qt(e.valueOf() - t.valueOf()))
        }
        ,
        e.prototype.computeTimeTop = function(e) {
            var t, n, r = this.positions, o = this.dateProfile, i = r.els.length, a = (e.milliseconds - en(o.slotMinTime)) / en(this.slotDuration);
            return a = Math.max(0, a),
            a = Math.min(i, a),
            t = Math.floor(a),
            n = a - (t = Math.min(t, i - 1)),
            r.tops[t] + r.getHeight(t) * n
        }
        ,
        e
    }()
      , eu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.options
              , o = e.slatElRefs;
            return Yo("tbody", null, e.slatMetas.map((function(i, a) {
                var s = {
                    time: i.time,
                    date: t.dateEnv.toDate(i.date),
                    view: t.viewApi
                }
                  , l = ["fc-timegrid-slot", "fc-timegrid-slot-lane", i.isLabeled ? "" : "fc-timegrid-slot-minor"];
                return Yo("tr", {
                    key: i.key,
                    ref: o.createRef(i.key)
                }, e.axis && Yo(Yl, r({}, i)), Yo(hi, {
                    hookProps: s,
                    classNames: n.slotLaneClassNames,
                    content: n.slotLaneContent,
                    didMount: n.slotLaneDidMount,
                    willUnmount: n.slotLaneWillUnmount
                }, (function(e, t, n, r) {
                    return Yo("td", {
                        ref: e,
                        className: l.concat(t).join(" "),
                        "data-time": i.isoTimeStr
                    }, r)
                }
                )))
            }
            )))
        }
        ,
        t
    }(ii)
      , tu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t.slatElRefs = new ls,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context;
            return Yo("div", {
                ref: this.rootElRef,
                className: "fc-timegrid-slots"
            }, Yo("table", {
                "aria-hidden": !0,
                className: t.theme.getClass("table"),
                style: {
                    minWidth: e.tableMinWidth,
                    width: e.clientWidth,
                    height: e.minHeight
                }
            }, e.tableColGroupNode, Yo(eu, {
                slatElRefs: this.slatElRefs,
                axis: e.axis,
                slatMetas: e.slatMetas
            })))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateSizing()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateSizing()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.props.onCoords && this.props.onCoords(null)
        }
        ,
        t.prototype.updateSizing = function() {
            var e, t = this.context, n = this.props;
            n.onCoords && null !== n.clientWidth && (this.rootElRef.current.offsetHeight && n.onCoords(new Ql(new zo(this.rootElRef.current,(e = this.slatElRefs.currentMap,
            n.slatMetas.map((function(t) {
                return e[t.key]
            }
            ))),!1,!0),this.props.dateProfile,t.options.slotDuration)))
        }
        ,
        t
    }(ii);
    function nu(e, t) {
        var n, r = [];
        for (n = 0; n < t; n += 1)
            r.push([]);
        if (e)
            for (n = 0; n < e.length; n += 1)
                r[e[n].col].push(e[n]);
        return r
    }
    function ru(e, t) {
        var n = [];
        if (e) {
            for (a = 0; a < t; a += 1)
                n[a] = {
                    affectedInstances: e.affectedInstances,
                    isEvent: e.isEvent,
                    segs: []
                };
            for (var r = 0, o = e.segs; r < o.length; r++) {
                var i = o[r];
                n[i.col].segs.push(i)
            }
        } else
            for (var a = 0; a < t; a += 1)
                n[a] = null;
        return n
    }
    var ou = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props;
            return Yo(Ls, {
                allDayDate: null,
                moreCnt: t.hiddenSegs.length,
                allSegs: t.hiddenSegs,
                hiddenSegs: t.hiddenSegs,
                alignmentElRef: this.rootElRef,
                defaultContent: iu,
                extraDateSpan: t.extraDateSpan,
                dateProfile: t.dateProfile,
                todayRange: t.todayRange,
                popoverContent: function() {
                    return gu(t.hiddenSegs, t)
                }
            }, (function(n, r, o, i, a, s, l, u) {
                return Yo("a", {
                    ref: function(t) {
                        li(n, t),
                        li(e.rootElRef, t)
                    },
                    className: ["fc-timegrid-more-link"].concat(r).join(" "),
                    style: {
                        top: t.top,
                        bottom: t.bottom
                    },
                    onClick: a,
                    title: s,
                    "aria-expanded": l,
                    "aria-controls": u
                }, Yo("div", {
                    ref: o,
                    className: "fc-timegrid-more-link-inner fc-sticky"
                }, i))
            }
            ))
        }
        ,
        t
    }(ii);
    function iu(e) {
        return e.shortText
    }
    function au(e, t, n) {
        var o = new fa;
        null != t && (o.strictOrder = t),
        null != n && (o.maxStackCnt = n);
        var i, a, s, l = ga(o.addSegs(e)), u = function(e) {
            var t = e.entriesByLevel
              , n = cu((function(e, t) {
                return e + ":" + t
            }
            ), (function(o, i) {
                var a = su(function(e, t, n) {
                    for (var r = e.levelCoords, o = e.entriesByLevel, i = o[t][n], a = r[t] + i.thickness, s = r.length, l = t; l < s && r[l] < a; l += 1)
                        ;
                    for (; l < s; l += 1) {
                        for (var u = o[l], c = void 0, d = Ea(u, i.span.start, ha), p = d[0] + d[1], f = p; (c = u[f]) && c.span.start < i.span.end; )
                            f += 1;
                        if (p < f)
                            return {
                                level: l,
                                lateralStart: p,
                                lateralEnd: f
                            }
                    }
                    return null
                }(e, o, i), n)
                  , s = t[o][i];
                return [r(r({}, s), {
                    nextLevelNodes: a[0]
                }), s.thickness + a[1]]
            }
            ));
            return su(t.length ? {
                level: 0,
                lateralStart: 0,
                lateralEnd: t[0].length
            } : null, n)[0]
        }(o);
        return i = u,
        a = 1,
        s = cu((function(e, t, n) {
            return va(e)
        }
        ), (function(e, t, n) {
            var o, i = e.nextLevelNodes, l = e.thickness, u = l + n, c = l / u, d = [];
            if (i.length)
                for (var p = 0, f = i; p < f.length; p++) {
                    var h = f[p];
                    if (void 0 === o)
                        o = (v = s(h, t, u))[0],
                        d.push(v[1]);
                    else {
                        var v = s(h, o, 0);
                        d.push(v[1])
                    }
                }
            else
                o = a;
            var g = (o - t) * c;
            return [o - g, r(r({}, e), {
                thickness: g,
                nextLevelNodes: d
            })]
        }
        )),
        {
            segRects: function(e) {
                var t = []
                  , n = cu((function(e, t, n) {
                    return va(e)
                }
                ), (function(e, n, i) {
                    var a = r(r({}, e), {
                        levelCoord: n,
                        stackDepth: i,
                        stackForward: 0
                    });
                    return t.push(a),
                    a.stackForward = o(e.nextLevelNodes, n + e.thickness, i + 1) + 1
                }
                ));
                function o(e, t, r) {
                    for (var o = 0, i = 0, a = e; i < a.length; i++) {
                        var s = a[i];
                        o = Math.max(n(s, t, r), o)
                    }
                    return o
                }
                return o(e, 0, 0),
                t
            }(u = i.map((function(e) {
                return s(e, 0, 0)[1]
            }
            ))),
            hiddenGroups: l
        }
    }
    function su(e, t) {
        if (!e)
            return [[], 0];
        for (var n = e.level, r = e.lateralStart, o = e.lateralEnd, i = r, a = []; i < o; )
            a.push(t(n, i)),
            i += 1;
        return a.sort(lu),
        [a.map(uu), a[0][1]]
    }
    function lu(e, t) {
        return t[1] - e[1]
    }
    function uu(e) {
        return e[0]
    }
    function cu(e, t) {
        var n = {};
        return function() {
            for (var r = [], o = 0; o < arguments.length; o++)
                r[o] = arguments[o];
            var i = e.apply(void 0, r);
            return i in n ? n[i] : n[i] = t.apply(void 0, r)
        }
    }
    function du(e, t, n, r) {
        void 0 === n && (n = null),
        void 0 === r && (r = 0);
        var o = [];
        if (n)
            for (var i = 0; i < e.length; i += 1) {
                var a = e[i]
                  , s = n.computeDateTop(a.start, t)
                  , l = Math.max(s + (r || 0), n.computeDateTop(a.end, t));
                o.push({
                    start: Math.round(s),
                    end: Math.round(l)
                })
            }
        return o
    }
    var pu = _n({
        hour: "numeric",
        minute: "2-digit",
        meridiem: !1
    })
      , fu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = ["fc-timegrid-event", "fc-v-event"];
            return this.props.isShort && e.push("fc-timegrid-event-short"),
            Yo(Rs, r({}, this.props, {
                defaultTimeFormat: pu,
                extraClassNames: e
            }))
        }
        ,
        t
    }(ii)
      , hu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props;
            return Yo(xs, {
                date: e.date,
                dateProfile: e.dateProfile,
                todayRange: e.todayRange,
                extraHookProps: e.extraHookProps
            }, (function(e, t) {
                return t && Yo("div", {
                    className: "fc-timegrid-col-misc",
                    ref: e
                }, t)
            }
            ))
        }
        ,
        t
    }(ii)
      , vu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.sortEventSegs = cn(Er),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = n.options.selectMirror
              , i = t.eventDrag && t.eventDrag.segs || t.eventResize && t.eventResize.segs || o && t.dateSelectionSegs || []
              , a = t.eventDrag && t.eventDrag.affectedInstances || t.eventResize && t.eventResize.affectedInstances || {}
              , s = this.sortEventSegs(t.fgEventSegs, n.options.eventOrder);
            return Yo(Ms, {
                elRef: t.elRef,
                date: t.date,
                dateProfile: t.dateProfile,
                todayRange: t.todayRange,
                extraHookProps: t.extraHookProps
            }, (function(n, l, u) {
                return Yo("td", r({
                    ref: n,
                    role: "gridcell",
                    className: ["fc-timegrid-col"].concat(l, t.extraClassNames || []).join(" ")
                }, u, t.extraDataAttrs), Yo("div", {
                    className: "fc-timegrid-col-frame"
                }, Yo("div", {
                    className: "fc-timegrid-col-bg"
                }, e.renderFillSegs(t.businessHourSegs, "non-business"), e.renderFillSegs(t.bgEventSegs, "bg-event"), e.renderFillSegs(t.dateSelectionSegs, "highlight")), Yo("div", {
                    className: "fc-timegrid-col-events"
                }, e.renderFgSegs(s, a, !1, !1, !1)), Yo("div", {
                    className: "fc-timegrid-col-events"
                }, e.renderFgSegs(i, {}, Boolean(t.eventDrag), Boolean(t.eventResize), Boolean(o))), Yo("div", {
                    className: "fc-timegrid-now-indicator-container"
                }, e.renderNowIndicator(t.nowIndicatorSegs)), Yo(hu, {
                    date: t.date,
                    dateProfile: t.dateProfile,
                    todayRange: t.todayRange,
                    extraHookProps: t.extraHookProps
                })))
            }
            ))
        }
        ,
        t.prototype.renderFgSegs = function(e, t, n, r, o) {
            var i = this.props;
            return i.forPrint ? gu(e, i) : this.renderPositionedFgSegs(e, t, n, r, o)
        }
        ,
        t.prototype.renderPositionedFgSegs = function(e, t, n, o, i) {
            var a = this
              , s = this.context.options
              , l = s.eventMaxStack
              , u = s.eventShortHeight
              , c = s.eventOrderStrict
              , d = s.eventMinHeight
              , p = this.props
              , f = p.date
              , h = p.slatCoords
              , v = p.eventSelection
              , g = p.todayRange
              , m = p.nowDate
              , y = n || o || i
              , S = function(e, t, n, r) {
                for (var o = [], i = [], a = 0; a < e.length; a += 1) {
                    var s = t[a];
                    s ? o.push({
                        index: a,
                        thickness: 1,
                        span: s
                    }) : i.push(e[a])
                }
                for (var l = au(o, n, r), u = l.segRects, c = l.hiddenGroups, d = [], p = 0, f = u; p < f.length; p++) {
                    var h = f[p];
                    d.push({
                        seg: e[h.index],
                        rect: h
                    })
                }
                for (var v = 0, g = i; v < g.length; v++) {
                    var m = g[v];
                    d.push({
                        seg: m,
                        rect: null
                    })
                }
                return {
                    segPlacements: d,
                    hiddenGroups: c
                }
            }(e, du(e, f, h, d), c, l)
              , E = S.segPlacements
              , b = S.hiddenGroups;
            return Yo(Ko, null, this.renderHiddenGroups(b, e), E.map((function(e) {
                var s = e.seg
                  , l = e.rect
                  , c = s.eventRange.instance.instanceId
                  , d = y || Boolean(!t[c] && l)
                  , p = mu(l && l.span)
                  , f = !y && l ? a.computeSegHStyle(l) : {
                    left: 0,
                    right: 0
                }
                  , h = Boolean(l) && l.stackForward > 0
                  , S = Boolean(l) && l.span.end - l.span.start < u;
                return Yo("div", {
                    className: "fc-timegrid-event-harness" + (h ? " fc-timegrid-event-harness-inset" : ""),
                    key: c,
                    style: r(r({
                        visibility: d ? "" : "hidden"
                    }, p), f)
                }, Yo(fu, r({
                    seg: s,
                    isDragging: n,
                    isResizing: o,
                    isDateSelecting: i,
                    isSelected: c === v,
                    isShort: S
                }, Tr(s, g, m))))
            }
            )))
        }
        ,
        t.prototype.renderHiddenGroups = function(e, t) {
            var n = this.props
              , r = n.extraDateSpan
              , o = n.dateProfile
              , i = n.todayRange
              , a = n.nowDate
              , s = n.eventSelection
              , l = n.eventDrag
              , u = n.eventResize;
            return Yo(Ko, null, e.map((function(e) {
                var n, c, d = mu(e.span), p = (n = e.entries,
                c = t,
                n.map((function(e) {
                    return c[e.index]
                }
                )));
                return Yo(ou, {
                    key: rn(zs(p)),
                    hiddenSegs: p,
                    top: d.top,
                    bottom: d.bottom,
                    extraDateSpan: r,
                    dateProfile: o,
                    todayRange: i,
                    nowDate: a,
                    eventSelection: s,
                    eventDrag: l,
                    eventResize: u
                })
            }
            )))
        }
        ,
        t.prototype.renderFillSegs = function(e, t) {
            var n = this.props
              , o = this.context
              , i = du(e, n.date, n.slatCoords, o.options.eventMinHeight).map((function(o, i) {
                var a = e[i];
                return Yo("div", {
                    key: xr(a.eventRange),
                    className: "fc-timegrid-bg-harness",
                    style: mu(o)
                }, "bg-event" === t ? Yo(Ps, r({
                    seg: a
                }, Tr(a, n.todayRange, n.nowDate))) : Is(t))
            }
            ));
            return Yo(Ko, null, i)
        }
        ,
        t.prototype.renderNowIndicator = function(e) {
            var t = this.props
              , n = t.slatCoords
              , r = t.date;
            return n ? e.map((function(e, t) {
                return Yo(Ts, {
                    isAxis: !1,
                    date: r,
                    key: t
                }, (function(t, o, i, a) {
                    return Yo("div", {
                        ref: t,
                        className: ["fc-timegrid-now-indicator-line"].concat(o).join(" "),
                        style: {
                            top: n.computeDateTop(e.start, r)
                        }
                    }, a)
                }
                ))
            }
            )) : null
        }
        ,
        t.prototype.computeSegHStyle = function(e) {
            var t, n, r = this.context, o = r.isRtl, i = r.options.slotEventOverlap, a = e.levelCoord, s = e.levelCoord + e.thickness;
            i && (s = Math.min(1, a + 2 * (s - a))),
            o ? (t = 1 - s,
            n = a) : (t = a,
            n = 1 - s);
            var l = {
                zIndex: e.stackDepth + 1,
                left: 100 * t + "%",
                right: 100 * n + "%"
            };
            return i && !e.stackForward && (l[o ? "marginLeft" : "marginRight"] = 20),
            l
        }
        ,
        t
    }(ii);
    function gu(e, t) {
        var n = t.todayRange
          , o = t.nowDate
          , i = t.eventSelection
          , a = t.eventDrag
          , s = t.eventResize
          , l = (a ? a.affectedInstances : null) || (s ? s.affectedInstances : null) || {};
        return Yo(Ko, null, e.map((function(e) {
            var t = e.eventRange.instance.instanceId;
            return Yo("div", {
                key: t,
                style: {
                    visibility: l[t] ? "hidden" : ""
                }
            }, Yo(fu, r({
                seg: e,
                isDragging: !1,
                isResizing: !1,
                isDateSelecting: !1,
                isSelected: t === i,
                isShort: !1
            }, Tr(e, n, o))))
        }
        )))
    }
    function mu(e) {
        return e ? {
            top: e.start,
            bottom: -e.end
        } : {
            top: "",
            bottom: ""
        }
    }
    var yu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.splitFgEventSegs = cn(nu),
            t.splitBgEventSegs = cn(nu),
            t.splitBusinessHourSegs = cn(nu),
            t.splitNowIndicatorSegs = cn(nu),
            t.splitDateSelectionSegs = cn(nu),
            t.splitEventDrag = cn(ru),
            t.splitEventResize = cn(ru),
            t.rootElRef = Xo(),
            t.cellElRefs = new ls,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context.options.nowIndicator && t.slatCoords && t.slatCoords.safeComputeTop(t.nowDate)
              , r = t.cells.length
              , o = this.splitFgEventSegs(t.fgEventSegs, r)
              , i = this.splitBgEventSegs(t.bgEventSegs, r)
              , a = this.splitBusinessHourSegs(t.businessHourSegs, r)
              , s = this.splitNowIndicatorSegs(t.nowIndicatorSegs, r)
              , l = this.splitDateSelectionSegs(t.dateSelectionSegs, r)
              , u = this.splitEventDrag(t.eventDrag, r)
              , c = this.splitEventResize(t.eventResize, r);
            return Yo("div", {
                className: "fc-timegrid-cols",
                ref: this.rootElRef
            }, Yo("table", {
                role: "presentation",
                style: {
                    minWidth: t.tableMinWidth,
                    width: t.clientWidth
                }
            }, t.tableColGroupNode, Yo("tbody", {
                role: "presentation"
            }, Yo("tr", {
                role: "row"
            }, t.axis && Yo("td", {
                "aria-hidden": !0,
                className: "fc-timegrid-col fc-timegrid-axis"
            }, Yo("div", {
                className: "fc-timegrid-col-frame"
            }, Yo("div", {
                className: "fc-timegrid-now-indicator-container"
            }, "number" == typeof n && Yo(Ts, {
                isAxis: !0,
                date: t.nowDate
            }, (function(e, t, r, o) {
                return Yo("div", {
                    ref: e,
                    className: ["fc-timegrid-now-indicator-arrow"].concat(t).join(" "),
                    style: {
                        top: n
                    }
                }, o)
            }
            ))))), t.cells.map((function(n, r) {
                return Yo(vu, {
                    key: n.key,
                    elRef: e.cellElRefs.createRef(n.key),
                    dateProfile: t.dateProfile,
                    date: n.date,
                    nowDate: t.nowDate,
                    todayRange: t.todayRange,
                    extraHookProps: n.extraHookProps,
                    extraDataAttrs: n.extraDataAttrs,
                    extraClassNames: n.extraClassNames,
                    extraDateSpan: n.extraDateSpan,
                    fgEventSegs: o[r],
                    bgEventSegs: i[r],
                    businessHourSegs: a[r],
                    nowIndicatorSegs: s[r],
                    dateSelectionSegs: l[r],
                    eventDrag: u[r],
                    eventResize: c[r],
                    slatCoords: t.slatCoords,
                    eventSelection: t.eventSelection,
                    forPrint: t.forPrint
                })
            }
            ))))))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateCoords()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateCoords()
        }
        ,
        t.prototype.updateCoords = function() {
            var e, t = this.props;
            t.onColCoords && null !== t.clientWidth && t.onColCoords(new zo(this.rootElRef.current,(e = this.cellElRefs.currentMap,
            t.cells.map((function(t) {
                return e[t.key]
            }
            ))),!0,!1))
        }
        ,
        t
    }(ii);
    var Su = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.processSlotOptions = cn(Eu),
            t.state = {
                slatCoords: null
            },
            t.handleRootEl = function(e) {
                e ? t.context.registerInteractiveComponent(t, {
                    el: e,
                    isHitComboAllowed: t.props.isHitComboAllowed
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t.handleScrollRequest = function(e) {
                var n = t.props.onScrollTopRequest
                  , r = t.state.slatCoords;
                if (n && r) {
                    if (e.time) {
                        var o = r.computeTimeTop(e.time);
                        (o = Math.ceil(o)) && (o += 1),
                        n(o)
                    }
                    return !0
                }
                return !1
            }
            ,
            t.handleColCoords = function(e) {
                t.colCoords = e
            }
            ,
            t.handleSlatCoords = function(e) {
                t.setState({
                    slatCoords: e
                }),
                t.props.onSlatCoords && t.props.onSlatCoords(e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.state;
            return Yo("div", {
                className: "fc-timegrid-body",
                ref: this.handleRootEl,
                style: {
                    width: e.clientWidth,
                    minWidth: e.tableMinWidth
                }
            }, Yo(tu, {
                axis: e.axis,
                dateProfile: e.dateProfile,
                slatMetas: e.slatMetas,
                clientWidth: e.clientWidth,
                minHeight: e.expandRows ? e.clientHeight : "",
                tableMinWidth: e.tableMinWidth,
                tableColGroupNode: e.axis ? e.tableColGroupNode : null,
                onCoords: this.handleSlatCoords
            }), Yo(yu, {
                cells: e.cells,
                axis: e.axis,
                dateProfile: e.dateProfile,
                businessHourSegs: e.businessHourSegs,
                bgEventSegs: e.bgEventSegs,
                fgEventSegs: e.fgEventSegs,
                dateSelectionSegs: e.dateSelectionSegs,
                eventSelection: e.eventSelection,
                eventDrag: e.eventDrag,
                eventResize: e.eventResize,
                todayRange: e.todayRange,
                nowDate: e.nowDate,
                nowIndicatorSegs: e.nowIndicatorSegs,
                clientWidth: e.clientWidth,
                tableMinWidth: e.tableMinWidth,
                tableColGroupNode: e.tableColGroupNode,
                slatCoords: t.slatCoords,
                onColCoords: this.handleColCoords,
                forPrint: e.forPrint
            }))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.scrollResponder = this.context.createScrollResponder(this.handleScrollRequest)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            this.scrollResponder.update(e.dateProfile !== this.props.dateProfile)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.scrollResponder.detach()
        }
        ,
        t.prototype.queryHit = function(e, t) {
            var n = this.context
              , o = n.dateEnv
              , i = n.options
              , a = this.colCoords
              , s = this.props.dateProfile
              , l = this.state.slatCoords
              , u = this.processSlotOptions(this.props.slotDuration, i.snapDuration)
              , c = u.snapDuration
              , d = u.snapsPerSlot
              , p = a.leftToIndex(e)
              , f = l.positions.topToIndex(t);
            if (null != p && null != f) {
                var h = this.props.cells[p]
                  , v = l.positions.tops[f]
                  , g = l.positions.getHeight(f)
                  , m = (t - v) / g
                  , y = f * d + Math.floor(m * d)
                  , S = this.props.cells[p].date
                  , E = Xt(s.slotMinTime, Kt(c, y))
                  , b = o.add(S, E)
                  , C = o.add(b, c);
                return {
                    dateProfile: s,
                    dateSpan: r({
                        range: {
                            start: b,
                            end: C
                        },
                        allDay: !1
                    }, h.extraDateSpan),
                    dayEl: a.els[p],
                    rect: {
                        left: a.lefts[p],
                        right: a.rights[p],
                        top: v,
                        bottom: v + g
                    },
                    layer: 0
                }
            }
            return null
        }
        ,
        t
    }(ui);
    function Eu(e, t) {
        var n = t || e
          , r = tn(e, n);
        return null === r && (n = e,
        r = 1),
        {
            snapDuration: n,
            snapsPerSlot: r
        }
    }
    var bu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.sliceRange = function(e, t) {
            for (var n = [], r = 0; r < t.length; r += 1) {
                var o = ur(e, t[r]);
                o && n.push({
                    start: o.start,
                    end: o.end,
                    isStart: o.start.valueOf() === e.start.valueOf(),
                    isEnd: o.end.valueOf() === e.end.valueOf(),
                    col: r
                })
            }
            return n
        }
        ,
        t
    }(Ka)
      , Cu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildDayRanges = cn(Du),
            t.slicer = new bu,
            t.timeColsRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = t.dateProfile
              , i = t.dayTableModel
              , a = n.options.nowIndicator
              , s = this.buildDayRanges(i, o, n.dateEnv);
            return Yo(Ga, {
                unit: a ? "minute" : "day"
            }, (function(l, u) {
                return Yo(Su, r({
                    ref: e.timeColsRef
                }, e.slicer.sliceProps(t, o, null, n, s), {
                    forPrint: t.forPrint,
                    axis: t.axis,
                    dateProfile: o,
                    slatMetas: t.slatMetas,
                    slotDuration: t.slotDuration,
                    cells: i.cells[0],
                    tableColGroupNode: t.tableColGroupNode,
                    tableMinWidth: t.tableMinWidth,
                    clientWidth: t.clientWidth,
                    clientHeight: t.clientHeight,
                    expandRows: t.expandRows,
                    nowDate: l,
                    nowIndicatorSegs: a && e.slicer.sliceNowDate(l, n, s),
                    todayRange: u,
                    onScrollTopRequest: t.onScrollTopRequest,
                    onSlatCoords: t.onSlatCoords
                }))
            }
            ))
        }
        ,
        t
    }(ui);
    function Du(e, t, n) {
        for (var r = [], o = 0, i = e.headerDates; o < i.length; o++) {
            var a = i[o];
            r.push({
                start: n.add(a, t.slotMinTime),
                end: n.add(a, t.slotMaxTime)
            })
        }
        return r
    }
    var Ru = [{
        hours: 1
    }, {
        minutes: 30
    }, {
        minutes: 15
    }, {
        seconds: 30
    }, {
        seconds: 15
    }];
    function wu(e, t, n, r, o) {
        for (var i = new Date(0), a = e, s = qt(0), l = n || function(e) {
            var t, n, r;
            for (t = Ru.length - 1; t >= 0; t -= 1)
                if (null !== (r = tn(n = qt(Ru[t]), e)) && r > 1)
                    return n;
            return e
        }(r), u = []; en(a) < en(t); ) {
            var c = o.add(i, a)
              , d = null !== tn(s, l);
            u.push({
                date: c,
                time: a,
                key: c.toISOString(),
                isoTimeStr: an(c),
                isLabeled: d
            }),
            a = Xt(a, r),
            s = Xt(s, r)
        }
        return u
    }
    var Tu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildTimeColsModel = cn(_u),
            t.buildSlatMetas = cn(wu),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.context
              , n = t.options
              , o = t.dateEnv
              , i = t.dateProfileGenerator
              , a = this.props
              , s = a.dateProfile
              , l = this.buildTimeColsModel(s, i)
              , u = this.allDaySplitter.splitProps(a)
              , c = this.buildSlatMetas(s.slotMinTime, s.slotMaxTime, n.slotLabelInterval, n.slotDuration, o)
              , d = n.dayMinWidth
              , p = !d
              , f = d
              , h = n.dayHeaders && Yo(qa, {
                dates: l.headerDates,
                dateProfile: s,
                datesRepDistinctDays: !0,
                renderIntro: p ? this.renderHeadAxis : null
            })
              , v = !1 !== n.allDaySlot && function(t) {
                return Yo(zl, r({}, u.allDay, {
                    dateProfile: s,
                    dayTableModel: l,
                    nextDayThreshold: n.nextDayThreshold,
                    tableMinWidth: t.tableMinWidth,
                    colGroupNode: t.tableColGroupNode,
                    renderRowIntro: p ? e.renderTableRowAxis : null,
                    showWeekNumbers: !1,
                    expandRows: !1,
                    headerAlignElRef: e.headerElRef,
                    clientWidth: t.clientWidth,
                    clientHeight: t.clientHeight,
                    forPrint: a.forPrint
                }, e.getAllDayMaxEventProps()))
            }
              , g = function(t) {
                return Yo(Cu, r({}, u.timed, {
                    dayTableModel: l,
                    dateProfile: s,
                    axis: p,
                    slotDuration: n.slotDuration,
                    slatMetas: c,
                    forPrint: a.forPrint,
                    tableColGroupNode: t.tableColGroupNode,
                    tableMinWidth: t.tableMinWidth,
                    clientWidth: t.clientWidth,
                    clientHeight: t.clientHeight,
                    onSlatCoords: e.handleSlatCoords,
                    expandRows: t.expandRows,
                    onScrollTopRequest: e.handleScrollTopRequest
                }))
            };
            return f ? this.renderHScrollLayout(h, v, g, l.colCnt, d, c, this.state.slatCoords) : this.renderSimpleLayout(h, v, g)
        }
        ,
        t
    }($l);
    function _u(e, t) {
        var n = new Za(e.renderRange,t);
        return new Xa(n,!1)
    }
    var xu = ci({
        initialView: "timeGridWeek",
        optionRefiners: {
            allDaySlot: Boolean
        },
        views: {
            timeGrid: {
                component: Tu,
                usesMinMaxTime: !0,
                allDaySlot: !0,
                slotDuration: "00:30:00",
                slotEventOverlap: !0
            },
            timeGridDay: {
                type: "timeGrid",
                duration: {
                    days: 1
                }
            },
            timeGridWeek: {
                type: "timeGrid",
                duration: {
                    weeks: 1
                }
            }
        }
    })
      , ku = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.state = {
                textId: Ve()
            },
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.context
              , t = e.theme
              , n = e.dateEnv
              , o = e.options
              , i = e.viewApi
              , a = this.props
              , s = a.cellId
              , l = a.dayDate
              , u = a.todayRange
              , c = this.state.textId
              , d = Ro(l, u)
              , p = o.listDayFormat ? n.format(l, o.listDayFormat) : ""
              , f = o.listDaySideFormat ? n.format(l, o.listDaySideFormat) : ""
              , h = r({
                date: n.toDate(l),
                view: i,
                textId: c,
                text: p,
                sideText: f,
                navLinkAttrs: ko(this.context, l),
                sideNavLinkAttrs: ko(this.context, l, "day", !1)
            }, d)
              , v = ["fc-list-day"].concat(wo(d, t));
            return Yo(hi, {
                hookProps: h,
                classNames: o.dayHeaderClassNames,
                content: o.dayHeaderContent,
                defaultContent: Mu,
                didMount: o.dayHeaderDidMount,
                willUnmount: o.dayHeaderWillUnmount
            }, (function(e, n, r, o) {
                return Yo("tr", {
                    ref: e,
                    className: v.concat(n).join(" "),
                    "data-date": on(l)
                }, Yo("th", {
                    scope: "colgroup",
                    colSpan: 3,
                    id: s,
                    "aria-labelledby": c
                }, Yo("div", {
                    className: "fc-list-day-cushion " + t.getClass("tableCellShaded"),
                    ref: r
                }, o)))
            }
            ))
        }
        ,
        t
    }(ii);
    function Mu(e) {
        return Yo(Ko, null, e.text && Yo("a", r({
            id: e.textId,
            className: "fc-list-day-text"
        }, e.navLinkAttrs), e.text), e.sideText && Yo("a", r({
            "aria-hidden": !0,
            className: "fc-list-day-side-text"
        }, e.sideNavLinkAttrs), e.sideText))
    }
    var Iu = _n({
        hour: "numeric",
        minute: "2-digit",
        meridiem: "short"
    })
      , Pu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = e.seg
              , o = e.timeHeaderId
              , i = e.eventHeaderId
              , a = e.dateHeaderId
              , s = t.options.eventTimeFormat || Iu;
            return Yo(Ds, {
                seg: n,
                timeText: "",
                disableDragging: !0,
                disableResizing: !0,
                defaultContent: function() {
                    return function(e, t) {
                        var n = kr(e, t);
                        return Yo("a", r({}, n), e.eventRange.def.title)
                    }(n, t)
                },
                isPast: e.isPast,
                isFuture: e.isFuture,
                isToday: e.isToday,
                isSelected: e.isSelected,
                isDragging: e.isDragging,
                isResizing: e.isResizing,
                isDateSelecting: e.isDateSelecting
            }, (function(e, r, l, u, c) {
                return Yo("tr", {
                    className: ["fc-list-event", c.event.url ? "fc-event-forced-url" : ""].concat(r).join(" "),
                    ref: e
                }, function(e, t, n, r, o) {
                    var i = n.options;
                    if (!1 !== i.displayEventTime) {
                        var a = e.eventRange.def
                          , s = e.eventRange.instance
                          , l = !1
                          , u = void 0;
                        if (a.allDay ? l = !0 : ir(e.eventRange.range) ? e.isStart ? u = wr(e, t, n, null, null, s.range.start, e.end) : e.isEnd ? u = wr(e, t, n, null, null, e.start, s.range.end) : l = !0 : u = wr(e, t, n),
                        l) {
                            var c = {
                                text: n.options.allDayText,
                                view: n.viewApi
                            };
                            return Yo(hi, {
                                hookProps: c,
                                classNames: i.allDayClassNames,
                                content: i.allDayContent,
                                defaultContent: Nu,
                                didMount: i.allDayDidMount,
                                willUnmount: i.allDayWillUnmount
                            }, (function(e, t, n, i) {
                                return Yo("td", {
                                    ref: e,
                                    headers: r + " " + o,
                                    className: ["fc-list-event-time"].concat(t).join(" ")
                                }, i)
                            }
                            ))
                        }
                        return Yo("td", {
                            className: "fc-list-event-time"
                        }, u)
                    }
                    return null
                }(n, s, t, o, a), Yo("td", {
                    "aria-hidden": !0,
                    className: "fc-list-event-graphic"
                }, Yo("span", {
                    className: "fc-list-event-dot",
                    style: {
                        borderColor: c.borderColor || c.backgroundColor
                    }
                })), Yo("td", {
                    ref: l,
                    headers: i + " " + a,
                    className: "fc-list-event-title"
                }, u))
            }
            ))
        }
        ,
        t
    }(ii);
    function Nu(e) {
        return e.text
    }
    var Hu = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.computeDateVars = cn(Au),
            t.eventStoreToSegs = cn(t._eventStoreToSegs),
            t.state = {
                timeHeaderId: Ve(),
                eventHeaderId: Ve(),
                dateHeaderIdRoot: Ve()
            },
            t.setRootEl = function(e) {
                e ? t.context.registerInteractiveComponent(t, {
                    el: e
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = ["fc-list", n.theme.getClass("table"), !1 !== n.options.stickyHeaderDates ? "fc-list-sticky" : ""]
              , o = this.computeDateVars(t.dateProfile)
              , i = o.dayDates
              , a = o.dayRanges
              , s = this.eventStoreToSegs(t.eventStore, t.eventUiBases, a);
            return Yo(Ci, {
                viewSpec: n.viewSpec,
                elRef: this.setRootEl
            }, (function(n, o) {
                return Yo("div", {
                    ref: n,
                    className: r.concat(o).join(" ")
                }, Yo(ss, {
                    liquid: !t.isHeightAuto,
                    overflowX: t.isHeightAuto ? "visible" : "hidden",
                    overflowY: t.isHeightAuto ? "visible" : "auto"
                }, s.length > 0 ? e.renderSegList(s, i) : e.renderEmptyMessage()))
            }
            ))
        }
        ,
        t.prototype.renderEmptyMessage = function() {
            var e = this.context
              , t = e.options
              , n = e.viewApi
              , r = {
                text: t.noEventsText,
                view: n
            };
            return Yo(hi, {
                hookProps: r,
                classNames: t.noEventsClassNames,
                content: t.noEventsContent,
                defaultContent: Ou,
                didMount: t.noEventsDidMount,
                willUnmount: t.noEventsWillUnmount
            }, (function(e, t, n, r) {
                return Yo("div", {
                    className: ["fc-list-empty"].concat(t).join(" "),
                    ref: e
                }, Yo("div", {
                    className: "fc-list-empty-cushion",
                    ref: n
                }, r))
            }
            ))
        }
        ,
        t.prototype.renderSegList = function(e, t) {
            var n = this.context
              , o = n.theme
              , i = n.options
              , a = this.state
              , s = a.timeHeaderId
              , l = a.eventHeaderId
              , u = a.dateHeaderIdRoot
              , c = function(e) {
                var t, n, r = [];
                for (t = 0; t < e.length; t += 1)
                    (r[(n = e[t]).dayIndex] || (r[n.dayIndex] = [])).push(n);
                return r
            }(e);
            return Yo(Ga, {
                unit: "day"
            }, (function(e, n) {
                for (var a = [], d = 0; d < c.length; d += 1) {
                    var p = c[d];
                    if (p) {
                        var f = on(t[d])
                          , h = u + "-" + f;
                        a.push(Yo(ku, {
                            key: f,
                            cellId: h,
                            dayDate: t[d],
                            todayRange: n
                        }));
                        for (var v = 0, g = p = Er(p, i.eventOrder); v < g.length; v++) {
                            var m = g[v];
                            a.push(Yo(Pu, r({
                                key: f + ":" + m.eventRange.instance.instanceId,
                                seg: m,
                                isDragging: !1,
                                isResizing: !1,
                                isDateSelecting: !1,
                                isSelected: !1,
                                timeHeaderId: s,
                                eventHeaderId: l,
                                dateHeaderId: h
                            }, Tr(m, n, e))))
                        }
                    }
                }
                return Yo("table", {
                    className: "fc-list-table " + o.getClass("table")
                }, Yo("thead", null, Yo("tr", null, Yo("th", {
                    scope: "col",
                    id: s
                }, i.timeHint), Yo("th", {
                    scope: "col",
                    "aria-hidden": !0
                }), Yo("th", {
                    scope: "col",
                    id: l
                }, i.eventHint))), Yo("tbody", null, a))
            }
            ))
        }
        ,
        t.prototype._eventStoreToSegs = function(e, t, n) {
            return this.eventRangesToSegs(hr(e, t, this.props.dateProfile.activeRange, this.context.options.nextDayThreshold).fg, n)
        }
        ,
        t.prototype.eventRangesToSegs = function(e, t) {
            for (var n = [], r = 0, o = e; r < o.length; r++) {
                var i = o[r];
                n.push.apply(n, this.eventRangeToSegs(i, t))
            }
            return n
        }
        ,
        t.prototype.eventRangeToSegs = function(e, t) {
            var n, r, o, i = this.context.dateEnv, a = this.context.options.nextDayThreshold, s = e.range, l = e.def.allDay, u = [];
            for (n = 0; n < t.length; n += 1)
                if ((r = ur(s, t[n])) && (o = {
                    component: this,
                    eventRange: e,
                    start: r.start,
                    end: r.end,
                    isStart: e.isStart && r.start.valueOf() === s.start.valueOf(),
                    isEnd: e.isEnd && r.end.valueOf() === s.end.valueOf(),
                    dayIndex: n
                },
                u.push(o),
                !o.isEnd && !l && n + 1 < t.length && s.end < i.add(t[n + 1].start, a))) {
                    o.end = s.end,
                    o.isEnd = !0;
                    break
                }
            return u
        }
        ,
        t
    }(ui);
    function Ou(e) {
        return e.text
    }
    function Au(e) {
        for (var t = bt(e.renderRange.start), n = e.renderRange.end, r = [], o = []; t < n; )
            r.push(t),
            o.push({
                start: t,
                end: ht(t, 1)
            }),
            t = ht(t, 1);
        return {
            dayDates: r,
            dayRanges: o
        }
    }
    function Wu(e) {
        return !1 === e ? null : _n(e)
    }
    var Lu = ci({
        optionRefiners: {
            listDayFormat: Wu,
            listDaySideFormat: Wu,
            noEventsClassNames: Wn,
            noEventsContent: Wn,
            noEventsDidMount: Wn,
            noEventsWillUnmount: Wn
        },
        views: {
            list: {
                component: Hu,
                buttonTextKey: "list",
                listDayFormat: {
                    month: "long",
                    day: "numeric",
                    year: "numeric"
                }
            },
            listDay: {
                type: "list",
                duration: {
                    days: 1
                },
                listDayFormat: {
                    weekday: "long"
                }
            },
            listWeek: {
                type: "list",
                duration: {
                    weeks: 1
                },
                listDayFormat: {
                    weekday: "long"
                },
                listDaySideFormat: {
                    month: "long",
                    day: "numeric",
                    year: "numeric"
                }
            },
            listMonth: {
                type: "list",
                duration: {
                    month: 1
                },
                listDaySideFormat: {
                    weekday: "long"
                }
            },
            listYear: {
                type: "list",
                duration: {
                    year: 1
                },
                listDaySideFormat: {
                    weekday: "long"
                }
            }
        }
    })
      , Uu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t
    }(jo);
    Uu.prototype.classes = {
        root: "fc-theme-bootstrap",
        table: "table-bordered",
        tableCellShaded: "table-active",
        buttonGroup: "btn-group",
        button: "btn btn-primary",
        buttonActive: "active",
        popover: "popover",
        popoverHeader: "popover-header",
        popoverContent: "popover-body"
    },
    Uu.prototype.baseIconClass = "fa",
    Uu.prototype.iconClasses = {
        close: "fa-times",
        prev: "fa-chevron-left",
        next: "fa-chevron-right",
        prevYear: "fa-angle-double-left",
        nextYear: "fa-angle-double-right"
    },
    Uu.prototype.rtlIconClasses = {
        prev: "fa-chevron-right",
        next: "fa-chevron-left",
        prevYear: "fa-angle-double-right",
        nextYear: "fa-angle-double-left"
    },
    Uu.prototype.iconOverrideOption = "bootstrapFontAwesome",
    Uu.prototype.iconOverrideCustomButtonOption = "bootstrapFontAwesome",
    Uu.prototype.iconOverridePrefix = "fa-";
    var Bu = ci({
        themeClasses: {
            bootstrap: Uu
        }
    })
      , zu = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t
    }(jo);
    zu.prototype.classes = {
        root: "fc-theme-bootstrap5",
        tableCellShaded: "fc-theme-bootstrap5-shaded",
        buttonGroup: "btn-group",
        button: "btn btn-primary",
        buttonActive: "active",
        popover: "popover",
        popoverHeader: "popover-header",
        popoverContent: "popover-body"
    },
    zu.prototype.baseIconClass = "bi",
    zu.prototype.iconClasses = {
        close: "bi-x-lg",
        prev: "bi-chevron-left",
        next: "bi-chevron-right",
        prevYear: "bi-chevron-double-left",
        nextYear: "bi-chevron-double-right"
    },
    zu.prototype.rtlIconClasses = {
        prev: "bi-chevron-right",
        next: "bi-chevron-left",
        prevYear: "bi-chevron-double-right",
        nextYear: "bi-chevron-double-left"
    },
    zu.prototype.iconOverrideOption = "buttonIcons",
    zu.prototype.iconOverrideCustomButtonOption = "icon",
    zu.prototype.iconOverridePrefix = "bi-";
    var Vu = ci({
        themeClasses: {
            bootstrap5: zu
        }
    })
      , Fu = "https://www.googleapis.com/calendar/v3/calendars";
    var Gu = ci({
        eventSourceDefs: [{
            parseMeta: function(e) {
                var t = e.googleCalendarId;
                return !t && e.url && (t = function(e) {
                    var t;
                    if (/^[^/]+@([^/.]+\.)*(google|googlemail|gmail)\.com$/.test(e))
                        return e;
                    if ((t = /^https:\/\/www.googleapis.com\/calendar\/v3\/calendars\/([^/]*)/.exec(e)) || (t = /^https?:\/\/www.google.com\/calendar\/feeds\/([^/]*)/.exec(e)))
                        return decodeURIComponent(t[1]);
                    return null
                }(e.url)),
                t ? {
                    googleCalendarId: t,
                    googleCalendarApiKey: e.googleCalendarApiKey,
                    googleCalendarApiBase: e.googleCalendarApiBase,
                    extraParams: e.extraParams
                } : null
            },
            fetch: function(e, t, n) {
                var o = e.context
                  , i = o.dateEnv
                  , a = o.options
                  , s = e.eventSource.meta
                  , l = s.googleCalendarApiKey || a.googleCalendarApiKey;
                if (l) {
                    var u = function(e) {
                        var t = e.googleCalendarApiBase;
                        t || (t = Fu);
                        return t + "/" + encodeURIComponent(e.googleCalendarId) + "/events"
                    }(s)
                      , c = s.extraParams
                      , d = "function" == typeof c ? c() : c
                      , p = function(e, t, n, o) {
                        var i, a, s;
                        o.canComputeOffset ? (a = o.formatIso(e.start),
                        s = o.formatIso(e.end)) : (a = ht(e.start, -1).toISOString(),
                        s = ht(e.end, 1).toISOString());
                        i = r(r({}, n || {}), {
                            key: t,
                            timeMin: a,
                            timeMax: s,
                            singleEvents: !0,
                            maxResults: 9999
                        }),
                        "local" !== o.timeZone && (i.timeZone = o.timeZone);
                        return i
                    }(e.range, l, d, i);
                    Yi("GET", u, p, (function(e, r) {
                        var o, i;
                        e.error ? n({
                            message: "Google Calendar API: " + e.error.message,
                            errors: e.error.errors,
                            xhr: r
                        }) : t({
                            rawEvents: (o = e.items,
                            i = p.timeZone,
                            o.map((function(e) {
                                return function(e, t) {
                                    var n = e.htmlLink || null;
                                    n && t && (n = function(e, t) {
                                        return e.replace(/(\?.*?)?(#|$)/, (function(e, n, r) {
                                            return (n ? n + "&" : "?") + t + r
                                        }
                                        ))
                                    }(n, "ctz=" + t));
                                    return {
                                        id: e.id,
                                        title: e.summary,
                                        start: e.start.dateTime || e.start.date,
                                        end: e.end.dateTime || e.end.date,
                                        url: n,
                                        location: e.location,
                                        description: e.description,
                                        attachments: e.attachments || [],
                                        extendedProps: (e.extendedProperties || {}).shared || {}
                                    }
                                }(e, i)
                            }
                            ))),
                            xhr: r
                        })
                    }
                    ), (function(e, t) {
                        n({
                            message: e,
                            xhr: t
                        })
                    }
                    ))
                } else
                    n({
                        message: "Specify a googleCalendarApiKey. See http://fullcalendar.io/docs/google_calendar/"
                    })
            }
        }],
        optionRefiners: {
            googleCalendarApiKey: String
        },
        eventSourceRefiners: {
            googleCalendarApiKey: String,
            googleCalendarId: String,
            googleCalendarApiBase: String,
            extraParams: Wn
        }
    })
      , ju = "2023-05-08"
      , qu = ["GPL-My-Project-Is-Open-Source", "CC-Attribution-NonCommercial-NoDerivatives"]
      , Yu = {
        position: "absolute",
        zIndex: 99999,
        bottom: "1px",
        left: "1px",
        background: "#eee",
        borderColor: "#ddd",
        borderStyle: "solid",
        borderWidth: "1px 1px 0 0",
        padding: "2px 4px",
        fontSize: "12px",
        borderTopRightRadius: "3px"
    };
    var Zu, Xu = ci({
        optionRefiners: {
            schedulerLicenseKey: String
        },
        viewContainerAppends: [function(e) {
            var t = e.options.schedulerLicenseKey
              , n = "undefined" != typeof window ? window.location.href : "";
            if (!/\w+:\/\/fullcalendar\.io\/|\/examples\/[\w-]+\.html$/.test(n)) {
                var r = function(e) {
                    if (-1 !== qu.indexOf(e))
                        return "valid";
                    var t = (e || "").match(/^(\d+)-fcs-(\d+)$/);
                    if (t && 10 === t[1].length) {
                        var n = new Date(1e3 * parseInt(t[2], 10))
                          , r = new Date(Ta.mockSchedulerReleaseDate || ju);
                        if (xt(r))
                            return ht(r, -372) < n ? "valid" : "outdated"
                    }
                    return "invalid"
                }(t);
                if ("valid" !== r)
                    return Yo("div", {
                        className: "fc-license-message",
                        style: Yu
                    }, "outdated" === r ? Yo(Ko, null, "Your license key is too old to work with this version. ", Yo("a", {
                        href: "http://fullcalendar.io/docs/schedulerLicenseKey#outdated"
                    }, "More Info")) : Yo(Ko, null, "Your license key is invalid. ", Yo("a", {
                        href: "http://fullcalendar.io/docs/schedulerLicenseKey#invalid"
                    }, "More Info")))
            }
            return null
        }
        ]
    }), Ku = "wheel mousewheel DomMouseScroll MozMousePixelScroll".split(" "), $u = function() {
        function e(e) {
            var t = this;
            this.el = e,
            this.emitter = new Bo,
            this.isScrolling = !1,
            this.isTouching = !1,
            this.isRecentlyWheeled = !1,
            this.isRecentlyScrolled = !1,
            this.wheelWaiter = new $i(this._handleWheelWaited.bind(this)),
            this.scrollWaiter = new $i(this._handleScrollWaited.bind(this)),
            this.handleScroll = function() {
                t.startScroll(),
                t.emitter.trigger("scroll", t.isRecentlyWheeled, t.isTouching),
                t.isRecentlyScrolled = !0,
                t.scrollWaiter.request(500)
            }
            ,
            this.handleWheel = function() {
                t.isRecentlyWheeled = !0,
                t.wheelWaiter.request(500)
            }
            ,
            this.handleTouchStart = function() {
                t.isTouching = !0
            }
            ,
            this.handleTouchEnd = function() {
                t.isTouching = !1,
                t.isRecentlyScrolled || t.endScroll()
            }
            ,
            e.addEventListener("scroll", this.handleScroll),
            e.addEventListener("touchstart", this.handleTouchStart, {
                passive: !0
            }),
            e.addEventListener("touchend", this.handleTouchEnd);
            for (var n = 0, r = Ku; n < r.length; n++) {
                var o = r[n];
                e.addEventListener(o, this.handleWheel)
            }
        }
        return e.prototype.destroy = function() {
            var e = this.el;
            e.removeEventListener("scroll", this.handleScroll),
            e.removeEventListener("touchstart", this.handleTouchStart, {
                passive: !0
            }),
            e.removeEventListener("touchend", this.handleTouchEnd);
            for (var t = 0, n = Ku; t < n.length; t++) {
                var r = n[t];
                e.removeEventListener(r, this.handleWheel)
            }
        }
        ,
        e.prototype.startScroll = function() {
            this.isScrolling || (this.isScrolling = !0,
            this.emitter.trigger("scrollStart", this.isRecentlyWheeled, this.isTouching))
        }
        ,
        e.prototype.endScroll = function() {
            this.isScrolling && (this.emitter.trigger("scrollEnd"),
            this.isScrolling = !1,
            this.isRecentlyScrolled = !0,
            this.isRecentlyWheeled = !1,
            this.scrollWaiter.clear(),
            this.wheelWaiter.clear())
        }
        ,
        e.prototype._handleScrollWaited = function() {
            this.isRecentlyScrolled = !1,
            this.isTouching || this.endScroll()
        }
        ,
        e.prototype._handleWheelWaited = function() {
            this.isRecentlyWheeled = !1
        }
        ,
        e
    }();
    function Ju(e) {
        var t = e.scrollLeft;
        if ("rtl" === window.getComputedStyle(e).direction)
            switch (ec()) {
            case "negative":
                t *= -1;
            case "reverse":
                t = e.scrollWidth - t - e.clientWidth
            }
        return t
    }
    function Qu(e, t) {
        if ("rtl" === window.getComputedStyle(e).direction)
            switch (ec()) {
            case "reverse":
                t = e.scrollWidth - t;
                break;
            case "negative":
                t = -(e.scrollWidth - t)
            }
        e.scrollLeft = t
    }
    function ec() {
        return Zu || (Zu = function() {
            var e, t = document.createElement("div");
            t.style.position = "absolute",
            t.style.top = "-1000px",
            t.style.width = "1px",
            t.style.height = "1px",
            t.style.overflow = "scroll",
            t.style.direction = "rtl",
            t.style.fontSize = "100px",
            t.innerHTML = "A",
            document.body.appendChild(t),
            t.scrollLeft > 0 ? e = "positive" : (t.scrollLeft = 1,
            e = t.scrollLeft > 0 ? "reverse" : "negative");
            return Ie(t),
            e
        }())
    }
    var tc, nc = "undefined" != typeof navigator && /Edge/.test(navigator.userAgent), rc = function() {
        function e(e, t) {
            var n = this;
            this.scrollEl = e,
            this.isRtl = t,
            this.usingRelative = null,
            this.updateSize = function() {
                var e = n.scrollEl
                  , t = He(e, ".fc-sticky")
                  , r = n.queryElGeoms(t)
                  , o = e.clientWidth
                  , i = e.clientHeight;
                n.usingRelative ? function(e, t, n, r, o) {
                    e.forEach((function(e, i) {
                        var a, s, l = t[i], u = l.naturalBound, c = l.parentBound, d = c.right - c.left, p = c.bottom - c.bottom;
                        d > r || p > o ? (a = n[i].left - u.left,
                        s = n[i].top - u.top) : (a = "",
                        s = ""),
                        We(e, {
                            position: "relative",
                            left: a,
                            right: -a,
                            top: s
                        })
                    }
                    ))
                }(t, r, n.computeElDestinations(r, o), o, i) : function(e, t, n) {
                    e.forEach((function(e, r) {
                        var o, i = t[r], a = i.textAlign, s = i.elWidth, l = i.parentBound, u = l.right - l.left;
                        We(e, {
                            left: o = "center" === a && u > n ? (n - s) / 2 : "",
                            right: o,
                            top: 0
                        })
                    }
                    ))
                }(t, r, o)
            }
            ,
            this.usingRelative = !function() {
                null == tc && (tc = function() {
                    var e = document.createElement("div");
                    e.style.position = "sticky",
                    document.body.appendChild(e);
                    var t = window.getComputedStyle(e).position;
                    return Ie(e),
                    "sticky" === t
                }());
                return tc
            }() || nc && t,
            this.usingRelative && (this.listener = new $u(e),
            this.listener.emitter.on("scrollEnd", this.updateSize))
        }
        return e.prototype.destroy = function() {
            this.listener && this.listener.destroy()
        }
        ,
        e.prototype.queryElGeoms = function(e) {
            for (var t = this.scrollEl, n = this.isRtl, r = function(e) {
                var t = e.getBoundingClientRect()
                  , n = Oo(e);
                return {
                    left: t.left + n.borderLeft + n.scrollbarLeft - Ju(e),
                    top: t.top + n.borderTop - e.scrollTop
                }
            }(t), o = [], i = 0, a = e; i < a.length; i++) {
                var s = a[i]
                  , l = go(Ao(s.parentNode, !0, !0), -r.left, -r.top)
                  , u = s.getBoundingClientRect()
                  , c = window.getComputedStyle(s)
                  , d = window.getComputedStyle(s.parentNode).textAlign
                  , p = null;
                "start" === d ? d = n ? "right" : "left" : "end" === d && (d = n ? "left" : "right"),
                "sticky" !== c.position && (p = go(u, -r.left - (parseFloat(c.left) || 0), -r.top - (parseFloat(c.top) || 0))),
                o.push({
                    parentBound: l,
                    naturalBound: p,
                    elWidth: u.width,
                    elHeight: u.height,
                    textAlign: d
                })
            }
            return o
        }
        ,
        e.prototype.computeElDestinations = function(e, t) {
            var n = this.scrollEl
              , r = n.scrollTop
              , o = Ju(n)
              , i = o + t;
            return e.map((function(e) {
                var t, n, a = e.elWidth, s = e.elHeight, l = e.parentBound, u = e.naturalBound;
                switch (e.textAlign) {
                case "left":
                    t = o;
                    break;
                case "right":
                    t = i - a;
                    break;
                case "center":
                    t = (o + i) / 2 - a / 2
                }
                return t = Math.min(t, l.right - a),
                t = Math.max(t, l.left),
                n = r,
                n = Math.min(n, l.bottom - s),
                {
                    left: t,
                    top: n = Math.max(n, u.top)
                }
            }
            ))
        }
        ,
        e
    }();
    var oc = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.elRef = Xo(),
            t.state = {
                xScrollbarWidth: 0,
                yScrollbarWidth: 0
            },
            t.handleScroller = function(e) {
                t.scroller = e,
                li(t.props.scrollerRef, e)
            }
            ,
            t.handleSizing = function() {
                var e = t.props;
                "scroll-hidden" === e.overflowY && t.setState({
                    yScrollbarWidth: t.scroller.getYScrollbarWidth()
                }),
                "scroll-hidden" === e.overflowX && t.setState({
                    xScrollbarWidth: t.scroller.getXScrollbarWidth()
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.props
              , n = e.state
              , r = e.context.isRtl && Po()
              , o = 0
              , i = 0
              , a = 0;
            return "scroll-hidden" === t.overflowX && (a = n.xScrollbarWidth),
            "scroll-hidden" === t.overflowY && null != n.yScrollbarWidth && (r ? o = n.yScrollbarWidth : i = n.yScrollbarWidth),
            Yo("div", {
                ref: this.elRef,
                className: "fc-scroller-harness" + (t.liquid ? " fc-scroller-harness-liquid" : "")
            }, Yo(ss, {
                ref: this.handleScroller,
                elRef: this.props.scrollerElRef,
                overflowX: "scroll-hidden" === t.overflowX ? "scroll" : t.overflowX,
                overflowY: "scroll-hidden" === t.overflowY ? "scroll" : t.overflowY,
                overcomeLeft: o,
                overcomeRight: i,
                overcomeBottom: a,
                maxHeight: "number" == typeof t.maxHeight ? t.maxHeight + ("scroll-hidden" === t.overflowX ? n.xScrollbarWidth : 0) : "",
                liquid: t.liquid,
                liquidIsAbsolute: !0
            }, t.children))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.handleSizing(),
            this.context.addResizeHandler(this.handleSizing)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            Wt(e, this.props) || this.handleSizing()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.context.removeResizeHandler(this.handleSizing)
        }
        ,
        t.prototype.needsXScrolling = function() {
            return this.scroller.needsXScrolling()
        }
        ,
        t.prototype.needsYScrolling = function() {
            return this.scroller.needsYScrolling()
        }
        ,
        t
    }(ii)
      , ic = function() {
        function e(e, t) {
            var n = this;
            this.isVertical = e,
            this.scrollEls = t,
            this.isPaused = !1,
            this.scrollListeners = t.map((function(e) {
                return n.bindScroller(e)
            }
            ))
        }
        return e.prototype.destroy = function() {
            for (var e = 0, t = this.scrollListeners; e < t.length; e++) {
                t[e].destroy()
            }
        }
        ,
        e.prototype.bindScroller = function(e) {
            var t = this
              , n = this.scrollEls
              , r = this.isVertical
              , o = new $u(e);
            return o.emitter.on("scroll", (function(o, i) {
                if (!t.isPaused && ((!t.masterEl || t.masterEl !== e && (o || i)) && t.assignMaster(e),
                t.masterEl === e))
                    for (var a = 0, s = n; a < s.length; a++) {
                        var l = s[a];
                        l !== e && (r ? l.scrollTop = e.scrollTop : l.scrollLeft = e.scrollLeft)
                    }
            }
            )),
            o.emitter.on("scrollEnd", (function() {
                t.masterEl === e && (t.masterEl = null)
            }
            )),
            o
        }
        ,
        e.prototype.assignMaster = function(e) {
            this.masterEl = e;
            for (var t = 0, n = this.scrollListeners; t < n.length; t++) {
                var r = n[t];
                r.el !== e && r.endScroll()
            }
        }
        ,
        e.prototype.forceScrollLeft = function(e) {
            this.isPaused = !0;
            for (var t = 0, n = this.scrollListeners; t < n.length; t++) {
                Qu(n[t].el, e)
            }
            this.isPaused = !1
        }
        ,
        e.prototype.forceScrollTop = function(e) {
            this.isPaused = !0;
            for (var t = 0, n = this.scrollListeners; t < n.length; t++) {
                n[t].el.scrollTop = e
            }
            this.isPaused = !1
        }
        ,
        e
    }()
      , ac = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.compileColGroupStats = pn(cc, fc),
            t.renderMicroColGroups = pn(hs),
            t.clippedScrollerRefs = new ls,
            t.scrollerElRefs = new ls(t._handleScrollerEl.bind(t)),
            t.chunkElRefs = new ls(t._handleChunkEl.bind(t)),
            t.stickyScrollings = [],
            t.scrollSyncersBySection = {},
            t.scrollSyncersByColumn = {},
            t.rowUnstableMap = new Map,
            t.rowInnerMaxHeightMap = new Map,
            t.anyRowHeightsChanged = !1,
            t.recentSizingCnt = 0,
            t.state = {
                shrinkWidths: [],
                forceYScrollbars: !1,
                forceXScrollbars: !1,
                scrollerClientWidths: {},
                scrollerClientHeights: {},
                sectionRowMaxHeights: []
            },
            t.handleSizing = function(e, n) {
                if (t.allowSizing()) {
                    n || (t.anyRowHeightsChanged = !0);
                    var o = {};
                    (e || !n && !t.rowUnstableMap.size) && (o.sectionRowMaxHeights = t.computeSectionRowMaxHeights()),
                    t.setState(r(r({
                        shrinkWidths: t.computeShrinkWidths()
                    }, t.computeScrollerDims()), o), (function() {
                        t.rowUnstableMap.size || t.updateStickyScrolling()
                    }
                    ))
                }
            }
            ,
            t.handleRowHeightChange = function(e, n) {
                var r = t
                  , o = r.rowUnstableMap
                  , i = r.rowInnerMaxHeightMap;
                if (n) {
                    o.delete(e);
                    var a = lc(e);
                    i.has(e) && i.get(e) === a || (i.set(e, a),
                    t.anyRowHeightsChanged = !0),
                    !o.size && t.anyRowHeightsChanged && (t.anyRowHeightsChanged = !1,
                    t.setState({
                        sectionRowMaxHeights: t.computeSectionRowMaxHeights()
                    }))
                } else
                    o.set(e, !0)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.props
              , n = e.state
              , r = e.context
              , i = n.shrinkWidths
              , a = this.compileColGroupStats(t.colGroups.map((function(e) {
                return [e]
            }
            )))
              , s = this.renderMicroColGroups(a.map((function(e, t) {
                return [e.cols, i[t]]
            }
            )))
              , l = ms(t.liquid, r)
              , u = this.getDims();
            u[0],
            u[1];
            for (var c, d = t.sections, p = d.length, f = 0, h = [], v = [], g = []; f < p && "header" === (c = d[f]).type; )
                h.push(this.renderSection(c, f, a, s, n.sectionRowMaxHeights, !0)),
                f += 1;
            for (; f < p && "body" === (c = d[f]).type; )
                v.push(this.renderSection(c, f, a, s, n.sectionRowMaxHeights, !1)),
                f += 1;
            for (; f < p && "footer" === (c = d[f]).type; )
                g.push(this.renderSection(c, f, a, s, n.sectionRowMaxHeights, !0)),
                f += 1;
            var m = !Eo()
              , y = {
                role: "rowgroup"
            };
            return Yo("table", {
                ref: t.elRef,
                role: "grid",
                className: l.join(" ")
            }, function(e, t) {
                var n = e.map((function(e, n) {
                    var r = e.width;
                    return "shrink" === r && (r = e.totalColWidth + vs(t[n]) + 1),
                    Yo("col", {
                        style: {
                            width: r
                        }
                    })
                }
                ));
                return Yo.apply(void 0, o(["colgroup", {}], n))
            }(a, i), Boolean(!m && h.length) && Yo.apply(void 0, o(["thead", y], h)), Boolean(!m && v.length) && Yo.apply(void 0, o(["tbody", y], v)), Boolean(!m && g.length) && Yo.apply(void 0, o(["tfoot", y], g)), m && Yo.apply(void 0, o(o(o(["tbody", y], h), v), g)))
        }
        ,
        t.prototype.renderSection = function(e, t, n, r, o, i) {
            var a = this;
            return "outerContent"in e ? Yo(Ko, {
                key: e.key
            }, e.outerContent) : Yo("tr", {
                key: e.key,
                role: "presentation",
                className: ys(e, this.props.liquid).join(" ")
            }, e.chunks.map((function(s, l) {
                return a.renderChunk(e, t, n[l], r[l], s, l, (o[t] || [])[l] || [], i)
            }
            )))
        }
        ,
        t.prototype.renderChunk = function(e, t, n, r, o, i, a, s) {
            if ("outerContent"in o)
                return Yo(Ko, {
                    key: o.key
                }, o.outerContent);
            var l = this.state
              , u = l.scrollerClientWidths
              , c = l.scrollerClientHeights
              , d = this.getDims()
              , p = d[0]
              , f = d[1]
              , h = t * f + i
              , v = i === (!this.context.isRtl || Po() ? f - 1 : 0)
              , g = t === p - 1
              , m = g && l.forceXScrollbars
              , y = v && l.forceYScrollbars
              , S = n && n.allowXScrolling
              , E = ds(this.props, e)
              , b = cs(this.props, e)
              , C = e.expandRows && b
              , D = ps(e, o, {
                tableColGroupNode: r,
                tableMinWidth: n && n.totalColMinWidth || "",
                clientWidth: void 0 !== u[h] ? u[h] : null,
                clientHeight: void 0 !== c[h] ? c[h] : null,
                expandRows: C,
                syncRowHeights: Boolean(e.syncRowHeights),
                rowSyncHeights: a,
                reportRowHeightChange: this.handleRowHeightChange
            }, s)
              , R = m ? g ? "scroll" : "scroll-hidden" : S ? g ? "auto" : "scroll-hidden" : "hidden"
              , w = y ? v ? "scroll" : "scroll-hidden" : E ? v ? "auto" : "scroll-hidden" : "hidden";
            return D = Yo(oc, {
                ref: this.clippedScrollerRefs.createRef(h),
                scrollerElRef: this.scrollerElRefs.createRef(h),
                overflowX: R,
                overflowY: w,
                liquid: b,
                maxHeight: e.maxHeight
            }, D),
            Yo(s ? "th" : "td", {
                key: o.key,
                ref: this.chunkElRefs.createRef(h),
                role: "presentation"
            }, D)
        }
        ,
        t.prototype.componentDidMount = function() {
            this.getStickyScrolling = pn(gc, null, mc),
            this.getScrollSyncersBySection = fn(hc.bind(this, !0), null, vc),
            this.getScrollSyncersByColumn = fn(hc.bind(this, !1), null, vc),
            this.updateScrollSyncers(),
            this.handleSizing(!1),
            this.context.addResizeHandler(this.handleSizing)
        }
        ,
        t.prototype.componentDidUpdate = function(e, t) {
            this.updateScrollSyncers(),
            this.handleSizing(!1, t.sectionRowMaxHeights !== this.state.sectionRowMaxHeights)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.context.removeResizeHandler(this.handleSizing),
            this.destroyStickyScrolling(),
            this.destroyScrollSyncers()
        }
        ,
        t.prototype.allowSizing = function() {
            var e = new Date;
            return !this.lastSizingDate || e.valueOf() > this.lastSizingDate.valueOf() + Ta.SCROLLGRID_RESIZE_INTERVAL ? (this.lastSizingDate = e,
            this.recentSizingCnt = 0,
            !0) : (this.recentSizingCnt += 1) <= 10
        }
        ,
        t.prototype.computeShrinkWidths = function() {
            var e = this
              , t = this.compileColGroupStats(this.props.colGroups.map((function(e) {
                return [e]
            }
            )))
              , n = this.getDims()
              , r = n[0]
              , o = n[1]
              , i = r * o
              , a = [];
            return t.forEach((function(t, n) {
                if (t.hasShrinkCol) {
                    var r = e.chunkElRefs.collect(n, i, o);
                    a[n] = us(r)
                }
            }
            )),
            a
        }
        ,
        t.prototype.computeSectionRowMaxHeights = function() {
            for (var e = new Map, t = this.getDims(), n = t[0], r = t[1], o = [], i = 0; i < n; i += 1) {
                var a = this.props.sections[i]
                  , s = [];
                if (a && a.syncRowHeights) {
                    for (var l = [], u = 0; u < r; u += 1) {
                        var c = i * r + u
                          , d = []
                          , p = this.chunkElRefs.currentMap[c];
                        d = p ? He(p, ".fc-scrollgrid-sync-table tr").map((function(t) {
                            var n = lc(t);
                            return e.set(t, n),
                            n
                        }
                        )) : [],
                        l.push(d)
                    }
                    var f = l[0].length
                      , h = !0;
                    for (u = 1; u < r; u += 1) {
                        if (!(a.chunks[u] && void 0 !== a.chunks[u].outerContent) && l[u].length !== f) {
                            h = !1;
                            break
                        }
                    }
                    if (h) {
                        for (u = 0; u < r; u += 1)
                            s.push([]);
                        for (E = 0; E < f; E += 1) {
                            var v = [];
                            for (u = 0; u < r; u += 1) {
                                var g = l[u][E];
                                null != g && v.push(g)
                            }
                            var m = Math.max.apply(Math, v);
                            for (u = 0; u < r; u += 1)
                                s[u].push(m)
                        }
                    } else {
                        for (var y = [], u = 0; u < r; u += 1)
                            y.push(sc(l[u]) + l[u].length);
                        for (var S = Math.max.apply(Math, y), u = 0; u < r; u += 1) {
                            var E, b = l[u].length, C = S - b, D = Math.floor(C / b), R = C - D * (b - 1), w = [];
                            for ((E = 0) < b && (w.push(R),
                            E += 1); E < b; )
                                w.push(D),
                                E += 1;
                            s.push(w)
                        }
                    }
                }
                o.push(s)
            }
            return this.rowInnerMaxHeightMap = e,
            o
        }
        ,
        t.prototype.computeScrollerDims = function() {
            for (var e = No(), t = this.getDims(), n = t[0], r = t[1], o = !this.context.isRtl || Po() ? r - 1 : 0, i = n - 1, a = this.clippedScrollerRefs.currentMap, s = this.scrollerElRefs.currentMap, l = !1, u = !1, c = {}, d = {}, p = 0; p < n; p += 1) {
                if ((h = a[v = p * r + o]) && h.needsYScrolling()) {
                    l = !0;
                    break
                }
            }
            for (var f = 0; f < r; f += 1) {
                var h;
                if ((h = a[v = i * r + f]) && h.needsXScrolling()) {
                    u = !0;
                    break
                }
            }
            for (p = 0; p < n; p += 1)
                for (f = 0; f < r; f += 1) {
                    var v, g = s[v = p * r + f];
                    if (g) {
                        var m = g.parentNode;
                        c[v] = Math.floor(m.getBoundingClientRect().width - (f === o && l ? e.y : 0)),
                        d[v] = Math.floor(m.getBoundingClientRect().height - (p === i && u ? e.x : 0))
                    }
                }
            return {
                forceYScrollbars: l,
                forceXScrollbars: u,
                scrollerClientWidths: c,
                scrollerClientHeights: d
            }
        }
        ,
        t.prototype.updateStickyScrolling = function() {
            var e = this.context.isRtl
              , t = this.scrollerElRefs.getAll().map((function(t) {
                return [t, e]
            }
            ))
              , n = this.getStickyScrolling(t);
            n.forEach((function(e) {
                return e.updateSize()
            }
            )),
            this.stickyScrollings = n
        }
        ,
        t.prototype.destroyStickyScrolling = function() {
            this.stickyScrollings.forEach(mc)
        }
        ,
        t.prototype.updateScrollSyncers = function() {
            for (var e = this.getDims(), t = e[0], n = e[1], r = t * n, o = {}, i = {}, a = this.scrollerElRefs.currentMap, s = 0; s < t; s += 1) {
                var l = s * n
                  , u = l + n;
                o[s] = zt(a, l, u, 1)
            }
            for (var c = 0; c < n; c += 1)
                i[c] = this.scrollerElRefs.collect(c, r, n);
            this.scrollSyncersBySection = this.getScrollSyncersBySection(o),
            this.scrollSyncersByColumn = this.getScrollSyncersByColumn(i)
        }
        ,
        t.prototype.destroyScrollSyncers = function() {
            Ht(this.scrollSyncersBySection, vc),
            Ht(this.scrollSyncersByColumn, vc)
        }
        ,
        t.prototype.getChunkConfigByIndex = function(e) {
            var t = this.getDims()[1]
              , n = Math.floor(e / t)
              , r = e % t
              , o = this.props.sections[n];
            return o && o.chunks[r]
        }
        ,
        t.prototype.forceScrollLeft = function(e, t) {
            var n = this.scrollSyncersByColumn[e];
            n && n.forceScrollLeft(t)
        }
        ,
        t.prototype.forceScrollTop = function(e, t) {
            var n = this.scrollSyncersBySection[e];
            n && n.forceScrollTop(t)
        }
        ,
        t.prototype._handleChunkEl = function(e, t) {
            var n = this.getChunkConfigByIndex(parseInt(t, 10));
            n && li(n.elRef, e)
        }
        ,
        t.prototype._handleScrollerEl = function(e, t) {
            var n = this.getChunkConfigByIndex(parseInt(t, 10));
            n && li(n.scrollerElRef, e)
        }
        ,
        t.prototype.getDims = function() {
            var e = this.props.sections.length;
            return [e, e ? this.props.sections[0].chunks.length : 0]
        }
        ,
        t
    }(ii);
    function sc(e) {
        for (var t = 0, n = 0, r = e; n < r.length; n++) {
            t += r[n]
        }
        return t
    }
    function lc(e) {
        var t = He(e, ".fc-scrollgrid-sync-inner").map(uc);
        return t.length ? Math.max.apply(Math, t) : 0
    }
    function uc(e) {
        return e.offsetHeight
    }
    function cc(e) {
        var t = dc(e.cols, "width")
          , n = dc(e.cols, "minWidth")
          , r = gs(e.cols);
        return {
            hasShrinkCol: r,
            totalColWidth: t,
            totalColMinWidth: n,
            allowXScrolling: "shrink" !== e.width && Boolean(t || n || r),
            cols: e.cols,
            width: e.width
        }
    }
    function dc(e, t) {
        for (var n = 0, r = 0, o = e; r < o.length; r++) {
            var i = o[r]
              , a = i[t];
            "number" == typeof a && (n += a * (i.span || 1))
        }
        return n
    }
    ac.addStateEquality({
        shrinkWidths: un,
        scrollerClientWidths: Wt,
        scrollerClientHeights: Wt
    });
    var pc = {
        cols: fs
    };
    function fc(e, t) {
        return Ut(e, t, pc)
    }
    function hc(e) {
        for (var t = [], n = 1; n < arguments.length; n++)
            t[n - 1] = arguments[n];
        return new ic(e,t)
    }
    function vc(e) {
        e.destroy()
    }
    function gc(e, t) {
        return new rc(e,t)
    }
    function mc(e) {
        e.destroy()
    }
    var yc = ci({
        deps: [Xu],
        scrollGridImpl: ac
    });
    Ta.SCROLLGRID_RESIZE_INTERVAL = 500,
    Ta.COLLAPSIBLE_WIDTH_THRESHOLD = 1200;
    var Sc = []
      , Ec = []
      , bc = ci({
        deps: [Xu],
        contextInit: function(e) {
            Sc.length || (window.addEventListener("beforeprint", Cc),
            window.addEventListener("afterprint", Dc)),
            Sc.push(e),
            e.calendarApi.on("_unmount", (function() {
                ln(Sc, e),
                Sc.length || (window.removeEventListener("beforeprint", Cc),
                window.removeEventListener("afterprint", Dc))
            }
            ))
        }
    });
    function Cc() {
        for (var e = He(document.body, ".fc-scroller-harness > .fc-scroller"), t = e.map((function(e) {
            var t = window.getComputedStyle(e);
            return {
                scrollLeft: e.scrollLeft,
                scrollTop: e.scrollTop,
                overflowX: t.overflowX,
                overflowY: t.overflowY,
                marginBottom: t.marginBottom
            }
        }
        )), n = 0, r = Sc; n < r.length; n++) {
            r[n].emitter.trigger("_beforeprint")
        }
        Qo((function() {
            !function(e, t) {
                e.forEach((function(e, n) {
                    e.style.overflowX = "visible",
                    e.style.overflowY = "visible",
                    e.style.marginBottom = "",
                    e.style.left = -t[n].scrollLeft + "px"
                }
                ))
            }(e, t),
            Ec.push((function() {
                return function(e, t) {
                    e.forEach((function(e, n) {
                        var r = t[n];
                        e.style.overflowX = r.overflowX,
                        e.style.overflowY = r.overflowY,
                        e.style.marginBottom = r.marginBottom,
                        e.style.left = "",
                        e.scrollLeft = r.scrollLeft,
                        e.scrollTop = r.scrollTop
                    }
                    ))
                }(e, t)
            }
            )),
            Ec.push(function() {
                var e = He(document.body, ".fc-scrollgrid");
                return e.forEach(Rc),
                function() {
                    return e.forEach(wc)
                }
            }())
        }
        ))
    }
    function Dc() {
        for (var e = 0, t = Sc; e < t.length; e++) {
            t[e].emitter.trigger("_afterprint")
        }
        Qo((function() {
            for (; Ec.length; )
                Ec.shift()()
        }
        ))
    }
    function Rc(e) {
        var t = e.getBoundingClientRect().width;
        (!e.classList.contains("fc-scrollgrid-collapsible") || t < Ta.COLLAPSIBLE_WIDTH_THRESHOLD) && (e.style.width = t + "px")
    }
    function wc(e) {
        e.style.width = ""
    }
    Ta.MAX_TIMELINE_SLOTS = 1e3;
    var Tc = [{
        years: 1
    }, {
        months: 1
    }, {
        days: 1
    }, {
        hours: 1
    }, {
        minutes: 30
    }, {
        minutes: 15
    }, {
        minutes: 10
    }, {
        minutes: 5
    }, {
        minutes: 1
    }, {
        seconds: 30
    }, {
        seconds: 15
    }, {
        seconds: 10
    }, {
        seconds: 5
    }, {
        seconds: 1
    }, {
        milliseconds: 500
    }, {
        milliseconds: 100
    }, {
        milliseconds: 10
    }, {
        milliseconds: 1
    }];
    function _c(e, t, n, r) {
        var o = {
            labelInterval: n.slotLabelInterval,
            slotDuration: n.slotDuration
        };
        !function(e, t, n) {
            var r = t.currentRange;
            if (e.labelInterval) {
                n.countDurationsBetween(r.start, r.end, e.labelInterval) > Ta.MAX_TIMELINE_SLOTS && (console.warn("slotLabelInterval results in too many cells"),
                e.labelInterval = null)
            }
            if (e.slotDuration) {
                n.countDurationsBetween(r.start, r.end, e.slotDuration) > Ta.MAX_TIMELINE_SLOTS && (console.warn("slotDuration results in too many cells"),
                e.slotDuration = null)
            }
            if (e.labelInterval && e.slotDuration) {
                var o = tn(e.labelInterval, e.slotDuration);
                (null === o || o < 1) && (console.warn("slotLabelInterval must be a multiple of slotDuration"),
                e.slotDuration = null)
            }
        }(o, e, t),
        Mc(o, e, t),
        function(e, t, n) {
            var r = t.currentRange
              , o = e.slotDuration;
            if (!o) {
                for (var i = Mc(e, t, n), a = 0, s = Tc; a < s.length; a++) {
                    var l = qt(s[a])
                      , u = tn(i, l);
                    if (null !== u && u > 1 && u <= 6) {
                        o = l;
                        break
                    }
                }
                if (o)
                    n.countDurationsBetween(r.start, r.end, o) > 200 && (o = null);
                o || (o = i),
                e.slotDuration = o
            }
        }(o, e, t);
        var i = n.slotLabelFormat
          , a = Array.isArray(i) ? i : null != i ? [i] : function(e, t, n, r) {
            var o, i, a = e.labelInterval, s = nn(a).unit, l = r.weekNumbers, u = o = i = null;
            "week" !== s || l || (s = "day");
            switch (s) {
            case "year":
                u = {
                    year: "numeric"
                };
                break;
            case "month":
                Ic("years", t, n) > 1 && (u = {
                    year: "numeric"
                }),
                o = {
                    month: "short"
                };
                break;
            case "week":
                Ic("years", t, n) > 1 && (u = {
                    year: "numeric"
                }),
                o = {
                    week: "narrow"
                };
                break;
            case "day":
                Ic("years", t, n) > 1 ? u = {
                    year: "numeric",
                    month: "long"
                } : Ic("months", t, n) > 1 && (u = {
                    month: "long"
                }),
                l && (o = {
                    week: "short"
                }),
                i = {
                    weekday: "narrow",
                    day: "numeric"
                };
                break;
            case "hour":
                l && (u = {
                    week: "short"
                }),
                Ic("days", t, n) > 1 && (o = {
                    weekday: "short",
                    day: "numeric",
                    month: "numeric",
                    omitCommas: !0
                }),
                i = {
                    hour: "numeric",
                    minute: "2-digit",
                    omitZeroMinute: !0,
                    meridiem: "short"
                };
                break;
            case "minute":
                Jt(a) / 60 >= 6 ? (u = {
                    hour: "numeric",
                    meridiem: "short"
                },
                o = function(e) {
                    return ":" + st(e.date.minute, 2)
                }
                ) : u = {
                    hour: "numeric",
                    minute: "numeric",
                    meridiem: "short"
                };
                break;
            case "second":
                Qt(a) / 60 >= 6 ? (u = {
                    hour: "numeric",
                    minute: "2-digit",
                    meridiem: "lowercase"
                },
                o = function(e) {
                    return ":" + st(e.date.second, 2)
                }
                ) : u = {
                    hour: "numeric",
                    minute: "2-digit",
                    second: "2-digit",
                    meridiem: "lowercase"
                };
                break;
            case "millisecond":
                u = {
                    hour: "numeric",
                    minute: "2-digit",
                    second: "2-digit",
                    meridiem: "lowercase"
                },
                o = function(e) {
                    return "." + st(e.millisecond, 3)
                }
            }
            return [].concat(u || [], o || [], i || [])
        }(o, e, t, n);
        o.headerFormats = a.map((function(e) {
            return _n(e)
        }
        )),
        o.isTimeScale = Boolean(o.slotDuration.milliseconds);
        var s = null;
        if (!o.isTimeScale) {
            var l = nn(o.slotDuration).unit;
            /year|month|week/.test(l) && (s = l)
        }
        o.largeUnit = s,
        o.emphasizeWeeks = 1 === Zt(o.slotDuration) && Ic("weeks", e, t) >= 2 && !n.businessHours;
        var u, c, d = n.snapDuration;
        d && (u = qt(d),
        c = tn(o.slotDuration, u)),
        null == c && (u = o.slotDuration,
        c = 1),
        o.snapDuration = u,
        o.snapsPerSlot = c;
        var p = en(e.slotMaxTime) - en(e.slotMinTime)
          , f = xc(e.renderRange.start, o, t)
          , h = xc(e.renderRange.end, o, t);
        o.isTimeScale && (f = t.add(f, e.slotMinTime),
        h = t.add(ht(h, -1), e.slotMaxTime)),
        o.timeWindowMs = p,
        o.normalizedRange = {
            start: f,
            end: h
        };
        for (var v = [], g = f; g < h; )
            kc(g, o, e, r) && v.push(g),
            g = t.add(g, o.slotDuration);
        o.slotDates = v;
        var m = -1
          , y = 0
          , S = []
          , E = [];
        for (g = f; g < h; )
            kc(g, o, e, r) ? (m += 1,
            S.push(m),
            E.push(y)) : S.push(m + .5),
            g = t.add(g, o.snapDuration),
            y += 1;
        return o.snapDiffToIndex = S,
        o.snapIndexToDiff = E,
        o.snapCnt = m + 1,
        o.slotCnt = o.snapCnt / o.snapsPerSlot,
        o.isWeekStarts = function(e, t) {
            for (var n = e.slotDates, r = e.emphasizeWeeks, o = null, i = [], a = 0, s = n; a < s.length; a++) {
                var l = s[a]
                  , u = t.computeWeekNumber(l)
                  , c = r && null !== o && o !== u;
                o = u,
                i.push(c)
            }
            return i
        }(o, t),
        o.cellRows = function(e, t) {
            for (var n = e.slotDates, r = e.headerFormats, o = r.map((function() {
                return []
            }
            )), i = Zt(e.slotDuration), a = 7 === i ? "week" : 1 === i ? "day" : null, s = r.map((function(e) {
                return e.getLargestUnit ? e.getLargestUnit() : null
            }
            )), l = 0; l < n.length; l += 1)
                for (var u = n[l], c = e.isWeekStarts[l], d = 0; d < r.length; d += 1) {
                    var p = r[d]
                      , f = o[d]
                      , h = f[f.length - 1]
                      , v = d === r.length - 1
                      , g = r.length > 1 && !v
                      , m = null
                      , y = s[d] || (v ? a : null);
                    if (g) {
                        var S = t.format(u, p);
                        h && h.text === S ? h.colspan += 1 : m = Pc(u, S, y)
                    } else if (!h || ct(t.countDurationsBetween(e.normalizedRange.start, u, e.labelInterval))) {
                        m = Pc(u, S = t.format(u, p), y)
                    } else
                        h.colspan += 1;
                    m && (m.weekStart = c,
                    f.push(m))
                }
            return o
        }(o, t),
        o.slotsPerLabel = tn(o.labelInterval, o.slotDuration),
        o
    }
    function xc(e, t, n) {
        var r = e;
        return t.isTimeScale || (r = bt(r),
        t.largeUnit && (r = n.startOf(r, t.largeUnit))),
        r
    }
    function kc(e, t, n, r) {
        if (r.isHiddenDay(e))
            return !1;
        if (t.isTimeScale) {
            var o = bt(e)
              , i = e.valueOf() - o.valueOf() - en(n.slotMinTime);
            return (i = (i % 864e5 + 864e5) % 864e5) < t.timeWindowMs
        }
        return !0
    }
    function Mc(e, t, n) {
        var r = t.currentRange
          , o = e.labelInterval;
        if (!o) {
            if (e.slotDuration) {
                for (var i = 0, a = Tc; i < a.length; i++) {
                    var s = qt(a[i])
                      , l = tn(s, e.slotDuration);
                    if (null !== l && l <= 6) {
                        o = s;
                        break
                    }
                }
                o || (o = e.slotDuration)
            } else
                for (var u = 0, c = Tc; u < c.length; u++) {
                    if (o = qt(c[u]),
                    n.countDurationsBetween(r.start, r.end, o) >= 18)
                        break
                }
            e.labelInterval = o
        }
        return o
    }
    function Ic(e, t, n) {
        var r = t.currentRange
          , o = null;
        return "years" === e ? o = n.diffWholeYears(r.start, r.end) : "months" === e || "weeks" === e ? o = n.diffWholeMonths(r.start, r.end) : "days" === e && (o = Et(r.start, r.end)),
        o || 0
    }
    function Pc(e, t, n) {
        return {
            date: e,
            text: t,
            rowUnit: n,
            colspan: 1,
            isWeekStart: !1
        }
    }
    var Nc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context;
            return Yo(gi, {
                hookProps: e.hookProps,
                content: t.options.slotLabelContent,
                defaultContent: Hc
            }, (function(t, n) {
                return Yo("a", r({
                    ref: t,
                    className: "fc-timeline-slot-cushion fc-scrollgrid-sync-inner" + (e.isSticky ? " fc-sticky" : "")
                }, e.navLinkAttrs), n)
            }
            ))
        }
        ,
        t
    }(ii);
    function Hc(e) {
        return e.text
    }
    function Oc(e) {
        return {
            level: e.level,
            date: e.dateEnv.toDate(e.dateMarker),
            view: e.viewApi,
            text: e.text
        }
    }
    var Ac = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.refineHookProps = dn(Oc),
            t.normalizeClassNames = Si(),
            t.buildCellNavLinkAttrs = cn(Wc),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = n.dateEnv
              , o = n.options
              , i = t.cell
              , a = t.dateProfile
              , s = t.tDateProfile
              , l = Ro(i.date, t.todayRange, t.nowDate, a)
              , u = ["fc-timeline-slot", "fc-timeline-slot-label"].concat("time" === i.rowUnit ? To(l, n.theme) : wo(l, n.theme));
            i.isWeekStart && u.push("fc-timeline-slot-em");
            var c = this.refineHookProps({
                level: t.rowLevel,
                dateMarker: i.date,
                text: i.text,
                dateEnv: n.dateEnv,
                viewApi: n.viewApi
            })
              , d = this.normalizeClassNames(o.slotLabelClassNames, c);
            return Yo(yi, {
                hookProps: c,
                didMount: o.slotLabelDidMount,
                willUnmount: o.slotLabelWillUnmount
            }, (function(o) {
                return Yo("th", {
                    ref: o,
                    className: u.concat(d).join(" "),
                    "data-date": r.formatIso(i.date, {
                        omitTime: !s.isTimeScale,
                        omitTimeZoneOffset: !0
                    }),
                    colSpan: i.colspan
                }, Yo("div", {
                    className: "fc-timeline-slot-frame",
                    style: {
                        height: t.rowInnerHeight
                    }
                }, Yo(Nc, {
                    hookProps: c,
                    isSticky: t.isSticky,
                    navLinkAttrs: e.buildCellNavLinkAttrs(n, i.date, i.rowUnit)
                })))
            }
            ))
        }
        ,
        t
    }(ii);
    function Wc(e, t, n) {
        return n && "time" !== n ? ko(e, t, n) : {}
    }
    var Lc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = e.dateProfile
              , n = e.tDateProfile
              , r = e.rowInnerHeights
              , o = e.todayRange
              , i = e.nowDate
              , a = n.cellRows;
            return Yo(Ko, null, a.map((function(e, s) {
                var l = s === a.length - 1
                  , u = n.isTimeScale && l;
                return Yo("tr", {
                    key: s,
                    className: ["fc-timeline-header-row", u ? "fc-timeline-header-row-chrono" : ""].join(" ")
                }, e.map((function(e) {
                    return Yo(Ac, {
                        key: e.date.toISOString(),
                        cell: e,
                        rowLevel: s,
                        dateProfile: t,
                        tDateProfile: n,
                        todayRange: o,
                        nowDate: i,
                        rowInnerHeight: r && r[s],
                        isSticky: !l
                    })
                }
                )))
            }
            )))
        }
        ,
        t
    }(ii)
      , Uc = function() {
        function e(e, t, n, r, o, i) {
            this.slatRootEl = e,
            this.dateProfile = n,
            this.tDateProfile = r,
            this.dateEnv = o,
            this.isRtl = i,
            this.outerCoordCache = new zo(e,t,!0,!1),
            this.innerCoordCache = new zo(e,Oe(t, "div"),!0,!1)
        }
        return e.prototype.isDateInRange = function(e) {
            return fr(this.dateProfile.currentRange, e)
        }
        ,
        e.prototype.dateToCoord = function(e) {
            var t = this.tDateProfile
              , n = this.computeDateSnapCoverage(e) / t.snapsPerSlot
              , r = Math.floor(n)
              , o = n - (r = Math.min(r, t.slotCnt - 1))
              , i = this.innerCoordCache
              , a = this.outerCoordCache;
            return this.isRtl ? a.originClientRect.width - (a.rights[r] - i.getWidth(r) * o) : a.lefts[r] + i.getWidth(r) * o
        }
        ,
        e.prototype.rangeToCoords = function(e) {
            return {
                start: this.dateToCoord(e.start),
                end: this.dateToCoord(e.end)
            }
        }
        ,
        e.prototype.durationToCoord = function(e) {
            var t = this
              , n = t.dateProfile
              , r = t.tDateProfile
              , o = t.dateEnv
              , i = t.isRtl
              , a = 0;
            if (n) {
                var s = o.add(n.activeRange.start, e);
                r.isTimeScale || (s = bt(s)),
                a = this.dateToCoord(s),
                !i && a && (a += 1)
            }
            return a
        }
        ,
        e.prototype.coordFromLeft = function(e) {
            return this.isRtl ? this.outerCoordCache.originClientRect.width - e : e
        }
        ,
        e.prototype.computeDateSnapCoverage = function(e) {
            return Bc(e, this.tDateProfile, this.dateEnv)
        }
        ,
        e
    }();
    function Bc(e, t, n) {
        var r = n.countDurationsBetween(t.normalizedRange.start, e, t.snapDuration);
        if (r < 0)
            return 0;
        if (r >= t.snapDiffToIndex.length)
            return t.snapCnt;
        var o = Math.floor(r)
          , i = t.snapDiffToIndex[o];
        return ct(i) ? i += r - o : i = Math.ceil(i),
        i
    }
    function zc(e, t) {
        return null === e ? {
            left: "",
            right: ""
        } : t ? {
            right: e,
            left: ""
        } : {
            left: e,
            right: ""
        }
    }
    function Vc(e, t) {
        return e ? t ? {
            right: e.start,
            left: -e.end
        } : {
            left: e.start,
            right: -e.end
        } : {
            left: "",
            right: ""
        }
    }
    var Fc = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = nn(t.tDateProfile.slotDuration).unit
              , o = t.slatCoords && t.slatCoords.dateProfile === t.dateProfile ? t.slatCoords : null;
            return Yo(Ga, {
                unit: r
            }, (function(r, i) {
                return Yo("div", {
                    className: "fc-timeline-header",
                    ref: e.rootElRef
                }, Yo("table", {
                    "aria-hidden": !0,
                    className: "fc-scrollgrid-sync-table",
                    style: {
                        minWidth: t.tableMinWidth,
                        width: t.clientWidth
                    }
                }, t.tableColGroupNode, Yo("tbody", null, Yo(Lc, {
                    dateProfile: t.dateProfile,
                    tDateProfile: t.tDateProfile,
                    nowDate: r,
                    todayRange: i,
                    rowInnerHeights: t.rowInnerHeights
                }))), n.options.nowIndicator && Yo("div", {
                    className: "fc-timeline-now-indicator-container"
                }, o && o.isDateInRange(r) && Yo(Ts, {
                    isAxis: !0,
                    date: r
                }, (function(e, t, i, a) {
                    return Yo("div", {
                        ref: e,
                        className: ["fc-timeline-now-indicator-arrow"].concat(t).join(" "),
                        style: zc(o.dateToCoord(r), n.isRtl)
                    }, a)
                }
                ))))
            }
            ))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateSize()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateSize()
        }
        ,
        t.prototype.updateSize = function() {
            this.props.onMaxCushionWidth && this.props.onMaxCushionWidth(this.computeMaxCushionWidth())
        }
        ,
        t.prototype.computeMaxCushionWidth = function() {
            return Math.max.apply(Math, He(this.rootElRef.current, ".fc-timeline-header-row:last-child .fc-timeline-slot-cushion").map((function(e) {
                return e.getBoundingClientRect().width
            }
            )))
        }
        ,
        t
    }(ii)
      , Gc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = t.dateEnv
              , o = t.options
              , i = t.theme
              , a = e.date
              , s = e.tDateProfile
              , l = e.isEm
              , u = Ro(e.date, e.todayRange, e.nowDate, e.dateProfile)
              , c = ["fc-timeline-slot", "fc-timeline-slot-lane"]
              , d = {
                "data-date": n.formatIso(a, {
                    omitTimeZoneOffset: !0,
                    omitTime: !s.isTimeScale
                })
            }
              , p = r(r({
                date: n.toDate(e.date)
            }, u), {
                view: t.viewApi
            });
            return l && c.push("fc-timeline-slot-em"),
            s.isTimeScale && c.push(ct(n.countDurationsBetween(s.normalizedRange.start, e.date, s.labelInterval)) ? "fc-timeline-slot-major" : "fc-timeline-slot-minor"),
            c.push.apply(c, e.isDay ? wo(u, i) : To(u, i)),
            Yo(hi, {
                hookProps: p,
                classNames: o.slotLaneClassNames,
                content: o.slotLaneContent,
                didMount: o.slotLaneDidMount,
                willUnmount: o.slotLaneWillUnmount,
                elRef: e.elRef
            }, (function(e, t, n, o) {
                return Yo("td", r({
                    ref: e,
                    className: c.concat(t).join(" ")
                }, d), Yo("div", {
                    ref: n
                }, o))
            }
            ))
        }
        ,
        t
    }(ii)
      , jc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = e.tDateProfile
              , n = e.cellElRefs
              , r = t.slotDates
              , o = t.isWeekStarts
              , i = !t.isTimeScale && !t.largeUnit;
            return Yo("tbody", null, Yo("tr", null, r.map((function(r, a) {
                var s = r.toISOString();
                return Yo(Gc, {
                    key: s,
                    elRef: n.createRef(s),
                    date: r,
                    dateProfile: e.dateProfile,
                    tDateProfile: t,
                    nowDate: e.nowDate,
                    todayRange: e.todayRange,
                    isEm: o[a],
                    isDay: i
                })
            }
            ))))
        }
        ,
        t
    }(ii)
      , qc = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t.cellElRefs = new ls,
            t.handleScrollRequest = function(e) {
                var n = t.props.onScrollLeftRequest
                  , r = t.coords;
                if (n && r) {
                    if (e.time)
                        n(r.coordFromLeft(r.durationToCoord(e.time)));
                    return !0
                }
                return null
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context;
            return Yo("div", {
                className: "fc-timeline-slots",
                ref: this.rootElRef
            }, Yo("table", {
                "aria-hidden": !0,
                className: t.theme.getClass("table"),
                style: {
                    minWidth: e.tableMinWidth,
                    width: e.clientWidth
                }
            }, e.tableColGroupNode, Yo(jc, {
                cellElRefs: this.cellElRefs,
                dateProfile: e.dateProfile,
                tDateProfile: e.tDateProfile,
                nowDate: e.nowDate,
                todayRange: e.todayRange
            })))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateSizing(),
            this.scrollResponder = this.context.createScrollResponder(this.handleScrollRequest)
        }
        ,
        t.prototype.componentDidUpdate = function(e) {
            this.updateSizing(),
            this.scrollResponder.update(e.dateProfile !== this.props.dateProfile)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.scrollResponder.detach(),
            this.props.onCoords && this.props.onCoords(null)
        }
        ,
        t.prototype.updateSizing = function() {
            var e, t = this.props, n = this.context;
            null !== t.clientWidth && this.scrollResponder && (this.rootElRef.current.offsetWidth && (this.coords = new Uc(this.rootElRef.current,(e = this.cellElRefs.currentMap,
            t.tDateProfile.slotDates.map((function(t) {
                var n = t.toISOString();
                return e[n]
            }
            ))),t.dateProfile,t.tDateProfile,n.dateEnv,n.isRtl),
            t.onCoords && t.onCoords(this.coords),
            this.scrollResponder.update(!1)))
        }
        ,
        t.prototype.positionToHit = function(e) {
            var t = this.coords.outerCoordCache
              , n = this.context
              , r = n.dateEnv
              , o = n.isRtl
              , i = this.props.tDateProfile
              , a = t.leftToIndex(e);
            if (null != a) {
                var s = t.getWidth(a)
                  , l = o ? (t.rights[a] - e) / s : (e - t.lefts[a]) / s
                  , u = Math.floor(l * i.snapsPerSlot)
                  , c = r.add(i.slotDates[a], Kt(i.snapDuration, u));
                return {
                    dateSpan: {
                        range: {
                            start: c,
                            end: r.add(c, i.snapDuration)
                        },
                        allDay: !this.props.tDateProfile.isTimeScale
                    },
                    dayEl: this.cellElRefs.currentMap[a],
                    left: t.lefts[a],
                    right: t.rights[a]
                }
            }
            return null
        }
        ,
        t
    }(ii);
    function Yc(e, t, n) {
        var r = [];
        if (n)
            for (var o = 0, i = e; o < i.length; o++) {
                var a = i[o]
                  , s = n.rangeToCoords(a)
                  , l = Math.round(s.start)
                  , u = Math.round(s.end);
                u - l < t && (u = l + t),
                r.push({
                    start: l,
                    end: u
                })
            }
        return r
    }
    var Zc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = [].concat(e.eventResizeSegs, e.dateSelectionSegs);
            return e.timelineCoords && Yo("div", {
                className: "fc-timeline-bg"
            }, this.renderSegs(e.businessHourSegs || [], e.timelineCoords, "non-business"), this.renderSegs(e.bgEventSegs || [], e.timelineCoords, "bg-event"), this.renderSegs(t, e.timelineCoords, "highlight"))
        }
        ,
        t.prototype.renderSegs = function(e, t, n) {
            var o = this.props
              , i = o.todayRange
              , a = o.nowDate
              , s = this.context.isRtl
              , l = Yc(e, 0, t)
              , u = e.map((function(e, t) {
                var o = Vc(l[t], s);
                return Yo("div", {
                    key: xr(e.eventRange),
                    className: "fc-timeline-bg-harness",
                    style: o
                }, "bg-event" === n ? Yo(Ps, r({
                    seg: e
                }, Tr(e, i, a))) : Is(n))
            }
            ));
            return Yo(Ko, null, u)
        }
        ,
        t
    }(ii)
      , Xc = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.sliceRange = function(e, t, n, r, o) {
            var i = function(e, t, n) {
                if (!t.isTimeScale && (e = or(e),
                t.largeUnit)) {
                    var r = e;
                    ((e = {
                        start: n.startOf(e.start, t.largeUnit),
                        end: n.startOf(e.end, t.largeUnit)
                    }).end.valueOf() !== r.end.valueOf() || e.end <= e.start) && (e = {
                        start: e.start,
                        end: n.add(e.end, t.slotDuration)
                    })
                }
                return e
            }(e, r, o)
              , a = [];
            if (Bc(i.start, r, o) < Bc(i.end, r, o)) {
                var s = ur(i, r.normalizedRange);
                s && a.push({
                    start: s.start,
                    end: s.end,
                    isStart: s.start.valueOf() === i.start.valueOf() && kc(s.start, r, t, n),
                    isEnd: s.end.valueOf() === i.end.valueOf() && kc(vt(s.end, -1), r, t, n)
                })
            }
            return a
        }
        ,
        t
    }(Ka)
      , Kc = _n({
        hour: "numeric",
        minute: "2-digit",
        omitZeroMinute: !0,
        meridiem: "narrow"
    })
      , $c = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props;
            return Yo(Rs, r({}, e, {
                extraClassNames: ["fc-timeline-event", "fc-h-event"],
                defaultTimeFormat: Kc,
                defaultDisplayEventTime: !e.isTimeScale
            }))
        }
        ,
        t
    }(ii)
      , Jc = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = t.hiddenSegs
              , i = t.elRef
              , a = t.placement
              , s = t.resourceId
              , l = a.top
              , u = a.hcoords
              , c = u && null !== l
              , d = Vc(u, n.isRtl)
              , p = s ? {
                resourceId: s
            } : {};
            return Yo(Ls, {
                allDayDate: null,
                moreCnt: o.length,
                allSegs: o,
                hiddenSegs: o,
                alignmentElRef: this.rootElRef,
                dateProfile: t.dateProfile,
                todayRange: t.todayRange,
                extraDateSpan: p,
                popoverContent: function() {
                    return Yo(Ko, null, o.map((function(e) {
                        var n = e.eventRange.instance.instanceId;
                        return Yo("div", {
                            key: n,
                            style: {
                                visibility: t.isForcedInvisible[n] ? "hidden" : ""
                            }
                        }, Yo($c, r({
                            isTimeScale: t.isTimeScale,
                            seg: e,
                            isDragging: !1,
                            isResizing: !1,
                            isDateSelecting: !1,
                            isSelected: n === t.eventSelection
                        }, Tr(e, t.todayRange, t.nowDate))))
                    }
                    )))
                }
            }, (function(t, n, o, a, s, u, p, f) {
                return Yo("a", {
                    ref: function(n) {
                        li(t, n),
                        li(i, n),
                        li(e.rootElRef, n)
                    },
                    className: ["fc-timeline-more-link"].concat(n).join(" "),
                    style: r({
                        visibility: c ? "" : "hidden",
                        top: l || 0
                    }, d),
                    onClick: s,
                    title: u,
                    "aria-expanded": p,
                    "aria-controls": f
                }, Yo("div", {
                    ref: o,
                    className: "fc-timeline-more-link-inner fc-sticky"
                }, a))
            }
            ))
        }
        ,
        t
    }(ii)
      , Qc = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.slicer = new Xc,
            t.sortEventSegs = cn(Er),
            t.harnessElRefs = new ls,
            t.moreElRefs = new ls,
            t.innerElRef = Xo(),
            t.state = {
                eventInstanceHeights: {},
                moreLinkHeights: {}
            },
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.props
              , n = e.state
              , r = e.context
              , o = r.options
              , i = t.dateProfile
              , a = t.tDateProfile
              , s = this.slicer.sliceProps(t, i, a.isTimeScale ? null : t.nextDayThreshold, r, i, r.dateProfileGenerator, a, r.dateEnv)
              , l = (s.eventDrag ? s.eventDrag.segs : null) || (s.eventResize ? s.eventResize.segs : null) || []
              , u = this.sortEventSegs(s.fgEventSegs, o.eventOrder)
              , c = function(e, t, n, r, o, i) {
                for (var a = [], s = [], l = 0; l < e.length; l += 1) {
                    var u = e[l]
                      , c = n[u.eventRange.instance.instanceId]
                      , d = t[l];
                    c && d ? a.push({
                        index: l,
                        span: d,
                        thickness: c
                    }) : s.push({
                        seg: u,
                        hcoords: d,
                        top: null
                    })
                }
                var p = new fa;
                null != o && (p.strictOrder = o),
                null != i && (p.maxStackCnt = i);
                var f = p.addSegs(a)
                  , h = f.map((function(t) {
                    return {
                        seg: e[t.index],
                        hcoords: t.span,
                        top: null
                    }
                }
                ))
                  , v = ga(f)
                  , g = []
                  , m = []
                  , y = function(t) {
                    return e[t.index]
                };
                for (l = 0; l < v.length; l += 1) {
                    var S = v[l]
                      , E = S.entries.map(y);
                    null != (c = r[rn(zs(E))]) ? g.push({
                        index: e.length + l,
                        thickness: c,
                        span: S.span
                    }) : m.push({
                        seg: E,
                        hcoords: S.span,
                        top: null
                    })
                }
                p.maxStackCnt = -1,
                p.addSegs(g);
                for (var b = [], C = 0, D = 0, R = p.toRects(); D < R.length; D++) {
                    var w = R[D]
                      , T = w.index;
                    b.push({
                        seg: T < e.length ? e[T] : v[T - e.length].entries.map(y),
                        hcoords: w.span,
                        top: w.levelCoord
                    }),
                    C = Math.max(C, w.levelCoord + w.thickness)
                }
                return [b.concat(s, h, m), C]
            }(u, Yc(u, o.eventMinWidth, t.timelineCoords), n.eventInstanceHeights, n.moreLinkHeights, o.eventOrderStrict, o.eventMaxStack)
              , d = c[0]
              , p = c[1]
              , f = (s.eventDrag ? s.eventDrag.affectedInstances : null) || (s.eventResize ? s.eventResize.affectedInstances : null) || {};
            return Yo(Ko, null, Yo(Zc, {
                businessHourSegs: s.businessHourSegs,
                bgEventSegs: s.bgEventSegs,
                timelineCoords: t.timelineCoords,
                eventResizeSegs: s.eventResize ? s.eventResize.segs : [],
                dateSelectionSegs: s.dateSelectionSegs,
                nowDate: t.nowDate,
                todayRange: t.todayRange
            }), Yo("div", {
                className: "fc-timeline-events fc-scrollgrid-sync-inner",
                ref: this.innerElRef,
                style: {
                    height: p
                }
            }, this.renderFgSegs(d, f, !1, !1, !1), this.renderFgSegs(function(e, t, n) {
                if (!e.length || !t)
                    return [];
                var r = function(e) {
                    for (var t = {}, n = 0, r = e; n < r.length; n++) {
                        var o = r[n]
                          , i = o.seg;
                        Array.isArray(i) || (t[i.eventRange.instance.instanceId] = o.top)
                    }
                    return t
                }(n);
                return e.map((function(e) {
                    return {
                        seg: e,
                        hcoords: t.rangeToCoords(e),
                        top: r[e.eventRange.instance.instanceId]
                    }
                }
                ))
            }(l, t.timelineCoords, d), {}, Boolean(s.eventDrag), Boolean(s.eventResize), !1)))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateSize()
        }
        ,
        t.prototype.componentDidUpdate = function(e, t) {
            e.eventStore === this.props.eventStore && e.timelineCoords === this.props.timelineCoords && t.moreLinkHeights === this.state.moreLinkHeights || this.updateSize()
        }
        ,
        t.prototype.updateSize = function() {
            var e = this.props
              , t = e.timelineCoords
              , n = this.innerElRef.current;
            e.onHeightChange && e.onHeightChange(n, !1),
            t && this.setState({
                eventInstanceHeights: Ht(this.harnessElRefs.currentMap, (function(e) {
                    return Math.round(e.getBoundingClientRect().height)
                }
                )),
                moreLinkHeights: Ht(this.moreElRefs.currentMap, (function(e) {
                    return Math.round(e.getBoundingClientRect().height)
                }
                ))
            }, (function() {
                e.onHeightChange && e.onHeightChange(n, !0)
            }
            )),
            e.syncParentMinHeight && (n.parentElement.style.minHeight = n.style.height)
        }
        ,
        t.prototype.renderFgSegs = function(e, t, n, o, i) {
            var a = this
              , s = a.harnessElRefs
              , l = a.moreElRefs
              , u = a.props
              , c = a.context
              , d = n || o || i;
            return Yo(Ko, null, e.map((function(e) {
                var a = e.seg
                  , p = e.hcoords
                  , f = e.top;
                if (Array.isArray(a)) {
                    var h = rn(zs(a));
                    return Yo(Jc, {
                        key: "m:" + h,
                        elRef: l.createRef(h),
                        hiddenSegs: a,
                        placement: e,
                        dateProfile: u.dateProfile,
                        nowDate: u.nowDate,
                        todayRange: u.todayRange,
                        isTimeScale: u.tDateProfile.isTimeScale,
                        eventSelection: u.eventSelection,
                        resourceId: u.resourceId,
                        isForcedInvisible: t
                    })
                }
                var v = a.eventRange.instance.instanceId
                  , g = d || Boolean(!t[v] && p && null !== f)
                  , m = Vc(p, c.isRtl);
                return Yo("div", {
                    key: "e:" + v,
                    ref: d ? null : s.createRef(v),
                    className: "fc-timeline-event-harness",
                    style: r({
                        visibility: g ? "" : "hidden",
                        top: f || 0
                    }, m)
                }, Yo($c, r({
                    isTimeScale: u.tDateProfile.isTimeScale,
                    seg: a,
                    isDragging: n,
                    isResizing: o,
                    isDateSelecting: i,
                    isSelected: v === u.eventSelection
                }, Tr(a, u.todayRange, u.nowDate))))
            }
            )))
        }
        ,
        t
    }(ii);
    Qc.addStateEquality({
        eventInstanceHeights: Wt,
        moreLinkHeights: Wt
    });
    var ed = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.slatsRef = Xo(),
            t.state = {
                coords: null
            },
            t.handeEl = function(e) {
                e ? t.context.registerInteractiveComponent(t, {
                    el: e
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t.handleCoords = function(e) {
                t.setState({
                    coords: e
                }),
                t.props.onSlatCoords && t.props.onSlatCoords(e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this
              , n = t.props
              , r = t.state
              , o = t.context
              , i = o.options
              , a = n.dateProfile
              , s = n.tDateProfile
              , l = nn(s.slotDuration).unit;
            return Yo("div", {
                className: "fc-timeline-body",
                ref: this.handeEl,
                style: {
                    minWidth: n.tableMinWidth,
                    height: n.clientHeight,
                    width: n.clientWidth
                }
            }, Yo(Ga, {
                unit: l
            }, (function(t, l) {
                return Yo(Ko, null, Yo(qc, {
                    ref: e.slatsRef,
                    dateProfile: a,
                    tDateProfile: s,
                    nowDate: t,
                    todayRange: l,
                    clientWidth: n.clientWidth,
                    tableColGroupNode: n.tableColGroupNode,
                    tableMinWidth: n.tableMinWidth,
                    onCoords: e.handleCoords,
                    onScrollLeftRequest: n.onScrollLeftRequest
                }), Yo(Qc, {
                    dateProfile: a,
                    tDateProfile: n.tDateProfile,
                    nowDate: t,
                    todayRange: l,
                    nextDayThreshold: i.nextDayThreshold,
                    businessHours: n.businessHours,
                    eventStore: n.eventStore,
                    eventUiBases: n.eventUiBases,
                    dateSelection: n.dateSelection,
                    eventSelection: n.eventSelection,
                    eventDrag: n.eventDrag,
                    eventResize: n.eventResize,
                    timelineCoords: r.coords,
                    syncParentMinHeight: !0
                }), i.nowIndicator && r.coords && r.coords.isDateInRange(t) && Yo("div", {
                    className: "fc-timeline-now-indicator-container"
                }, Yo(Ts, {
                    isAxis: !1,
                    date: t
                }, (function(e, n, i, a) {
                    return Yo("div", {
                        ref: e,
                        className: ["fc-timeline-now-indicator-line"].concat(n).join(" "),
                        style: zc(r.coords.dateToCoord(t), o.isRtl)
                    }, a)
                }
                ))))
            }
            )))
        }
        ,
        t.prototype.queryHit = function(e, t, n, r) {
            var o = this.slatsRef.current.positionToHit(e);
            return o ? {
                dateProfile: this.props.dateProfile,
                dateSpan: o.dateSpan,
                rect: {
                    left: o.left,
                    right: o.right,
                    top: 0,
                    bottom: r
                },
                dayEl: o.dayEl,
                layer: 0
            } : null
        }
        ,
        t
    }(ui)
      , td = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildTimelineDateProfile = cn(_c),
            t.scrollGridRef = Xo(),
            t.state = {
                slatCoords: null,
                slotCushionMaxWidth: null
            },
            t.handleSlatCoords = function(e) {
                t.setState({
                    slatCoords: e
                })
            }
            ,
            t.handleScrollLeftRequest = function(e) {
                t.scrollGridRef.current.forceScrollLeft(0, e)
            }
            ,
            t.handleMaxCushionWidth = function(e) {
                t.setState({
                    slotCushionMaxWidth: Math.ceil(e)
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this
              , n = t.props
              , o = t.state
              , i = t.context
              , a = i.options
              , s = !n.forPrint && Es(a)
              , l = !n.forPrint && bs(a)
              , u = this.buildTimelineDateProfile(n.dateProfile, i.dateEnv, a, i.dateProfileGenerator)
              , c = ["fc-timeline", !1 === a.eventOverlap ? "fc-timeline-overlap-disabled" : ""]
              , d = a.slotMinWidth
              , p = nd(u, d || this.computeFallbackSlotMinWidth(u))
              , f = [{
                type: "header",
                key: "header",
                isSticky: s,
                chunks: [{
                    key: "timeline",
                    content: function(t) {
                        return Yo(Fc, {
                            dateProfile: n.dateProfile,
                            clientWidth: t.clientWidth,
                            clientHeight: t.clientHeight,
                            tableMinWidth: t.tableMinWidth,
                            tableColGroupNode: t.tableColGroupNode,
                            tDateProfile: u,
                            slatCoords: o.slatCoords,
                            onMaxCushionWidth: d ? null : e.handleMaxCushionWidth
                        })
                    }
                }]
            }, {
                type: "body",
                key: "body",
                liquid: !0,
                chunks: [{
                    key: "timeline",
                    content: function(t) {
                        return Yo(ed, r({}, n, {
                            clientWidth: t.clientWidth,
                            clientHeight: t.clientHeight,
                            tableMinWidth: t.tableMinWidth,
                            tableColGroupNode: t.tableColGroupNode,
                            tDateProfile: u,
                            onSlatCoords: e.handleSlatCoords,
                            onScrollLeftRequest: e.handleScrollLeftRequest
                        }))
                    }
                }]
            }];
            return l && f.push({
                type: "footer",
                key: "footer",
                isSticky: !0,
                chunks: [{
                    key: "timeline",
                    content: Ss
                }]
            }),
            Yo(Ci, {
                viewSpec: i.viewSpec
            }, (function(t, r) {
                return Yo("div", {
                    ref: t,
                    className: c.concat(r).join(" ")
                }, Yo(ac, {
                    ref: e.scrollGridRef,
                    liquid: !n.isHeightAuto && !n.forPrint,
                    collapsibleWidth: !1,
                    colGroups: [{
                        cols: p
                    }],
                    sections: f
                }))
            }
            ))
        }
        ,
        t.prototype.computeFallbackSlotMinWidth = function(e) {
            return Math.max(30, (this.state.slotCushionMaxWidth || 0) / e.slotsPerLabel)
        }
        ,
        t
    }(ui);
    function nd(e, t) {
        return [{
            span: e.slotCnt,
            minWidth: t || 1
        }]
    }
    var rd = ci({
        deps: [Xu],
        initialView: "timelineDay",
        views: {
            timeline: {
                component: td,
                usesMinMaxTime: !0,
                eventResizableFromStart: !0
            },
            timelineDay: {
                type: "timeline",
                duration: {
                    days: 1
                }
            },
            timelineWeek: {
                type: "timeline",
                duration: {
                    weeks: 1
                }
            },
            timelineMonth: {
                type: "timeline",
                duration: {
                    months: 1
                }
            },
            timelineYear: {
                type: "timeline",
                duration: {
                    years: 1
                }
            }
        }
    });
    function od(e, t) {
        var n = e.resourceEditable;
        if (null == n) {
            var r = e.sourceId && t.getCurrentData().eventSources[e.sourceId];
            r && (n = r.extendedProps.resourceEditable),
            null == n && null == (n = t.options.eventResourceEditable) && (n = t.options.editable)
        }
        return n
    }
    var id = function() {
        function e() {
            this.filterResources = cn(ad)
        }
        return e.prototype.transform = function(e, t) {
            return t.viewSpec.optionDefaults.needsResourceData ? {
                resourceStore: this.filterResources(t.resourceStore, t.options.filterResourcesWithEvents, t.eventStore, t.dateProfile.activeRange),
                resourceEntityExpansions: t.resourceEntityExpansions
            } : null
        }
        ,
        e
    }();
    function ad(e, t, n, o) {
        if (t) {
            var i = function(e, t) {
                var n = {};
                for (var r in e)
                    for (var o = 0, i = t[e[r].defId].resourceIds; o < i.length; o++) {
                        n[i[o]] = !0
                    }
                return n
            }(function(e, t) {
                return Nt(e, (function(e) {
                    return dr(e.range, t)
                }
                ))
            }(n.instances, o), n.defs);
            return r(i, function(e, t) {
                var n = {};
                for (var r in e)
                    for (var o = void 0; (o = t[r]) && (r = o.parentId); )
                        n[r] = !0;
                return n
            }(i, e)),
            Nt(e, (function(e, t) {
                return i[t]
            }
            ))
        }
        return e
    }
    var sd = function() {
        function e() {
            this.buildResourceEventUis = cn(ld, Wt),
            this.injectResourceEventUis = cn(ud)
        }
        return e.prototype.transform = function(e, t) {
            return t.viewSpec.optionDefaults.needsResourceData ? null : {
                eventUiBases: this.injectResourceEventUis(e.eventUiBases, e.eventStore.defs, this.buildResourceEventUis(t.resourceStore))
            }
        }
        ,
        e
    }();
    function ld(e) {
        return Ht(e, (function(e) {
            return e.ui
        }
        ))
    }
    function ud(e, t, n) {
        return Ht(e, (function(e, r) {
            return r ? function(e, t, n) {
                for (var r = [], o = 0, i = t.resourceIds; o < i.length; o++) {
                    var a = i[o];
                    n[a] && r.unshift(n[a])
                }
                return r.unshift(e),
                Zn(r)
            }(e, t[r], n) : e
        }
        ))
    }
    var cd = [];
    function dd(e) {
        cd.push(e)
    }
    function pd(e) {
        return cd[e]
    }
    function fd() {
        return cd
    }
    var hd = {
        id: String,
        resources: Wn,
        url: String,
        method: String,
        startParam: String,
        endParam: String,
        timeZoneParam: String,
        extraParams: Wn
    };
    function vd(e) {
        var t;
        if ("string" == typeof e ? t = {
            url: e
        } : "function" == typeof e || Array.isArray(e) ? t = {
            resources: e
        } : "object" == typeof e && e && (t = e),
        t) {
            var n = An(t, hd)
              , r = n.refined;
            !function(e) {
                for (var t in e)
                    console.warn("Unknown resource prop '" + t + "'")
            }(n.extra);
            var o = function(e) {
                for (var t = fd(), n = t.length - 1; n >= 0; n -= 1) {
                    var r = t[n].parseMeta(e);
                    if (r)
                        return {
                            meta: r,
                            sourceDefId: n
                        }
                }
                return null
            }(r);
            if (o)
                return {
                    _raw: e,
                    sourceId: Ke(),
                    sourceDefId: o.sourceDefId,
                    meta: o.meta,
                    publicId: r.id || "",
                    isFetching: !1,
                    latestFetchId: "",
                    fetchRange: null
                }
        }
        return null
    }
    function gd(e, t, n) {
        var o = n.options
          , i = n.dateProfile;
        if (!e || !t)
            return md(o.initialResources || o.resources, i.activeRange, o.refetchResourcesOnNavigate, n);
        switch (t.type) {
        case "RESET_RESOURCE_SOURCE":
            return md(t.resourceSourceInput, i.activeRange, o.refetchResourcesOnNavigate, n);
        case "PREV":
        case "NEXT":
        case "CHANGE_DATE":
        case "CHANGE_VIEW_TYPE":
            return function(e, t, n, r) {
                if (n && !function(e) {
                    return Boolean(pd(e.sourceDefId).ignoreRange)
                }(e) && (!e.fetchRange || !cr(e.fetchRange, t)))
                    return yd(e, t, r);
                return e
            }(e, i.activeRange, o.refetchResourcesOnNavigate, n);
        case "RECEIVE_RESOURCES":
        case "RECEIVE_RESOURCE_ERROR":
            return function(e, t, n) {
                if (t === e.latestFetchId)
                    return r(r({}, e), {
                        isFetching: !1,
                        fetchRange: n
                    });
                return e
            }(e, t.fetchId, t.fetchRange);
        case "REFETCH_RESOURCES":
            return yd(e, i.activeRange, n);
        default:
            return e
        }
    }
    function md(e, t, n, r) {
        if (e) {
            var o = vd(e);
            return o = yd(o, n ? t : null, r)
        }
        return null
    }
    function yd(e, t, n) {
        var o = pd(e.sourceDefId)
          , i = Ke();
        return o.fetch({
            resourceSource: e,
            range: t,
            context: n
        }, (function(e) {
            n.dispatch({
                type: "RECEIVE_RESOURCES",
                fetchId: i,
                fetchRange: t,
                rawResources: e.rawResources
            })
        }
        ), (function(e) {
            n.dispatch({
                type: "RECEIVE_RESOURCE_ERROR",
                fetchId: i,
                fetchRange: t,
                error: e
            })
        }
        )),
        r(r({}, e), {
            isFetching: !0,
            latestFetchId: i
        })
    }
    var Sd = "_fc:"
      , Ed = {
        id: String,
        parentId: String,
        children: Wn,
        title: String,
        businessHours: Wn,
        extendedProps: Wn,
        eventEditable: Boolean,
        eventStartEditable: Boolean,
        eventDurationEditable: Boolean,
        eventConstraint: Wn,
        eventOverlap: Boolean,
        eventAllow: Wn,
        eventClassNames: Gn,
        eventBackgroundColor: String,
        eventBorderColor: String,
        eventTextColor: String,
        eventColor: String
    };
    function bd(e, t, n, o) {
        void 0 === t && (t = "");
        var i = An(e, Ed)
          , a = i.refined
          , s = i.extra
          , l = {
            id: a.id || Sd + Ke(),
            parentId: a.parentId || t,
            title: a.title || "",
            businessHours: a.businessHours ? fo(a.businessHours, o) : null,
            ui: Yn({
                editable: a.eventEditable,
                startEditable: a.eventStartEditable,
                durationEditable: a.eventDurationEditable,
                constraint: a.eventConstraint,
                overlap: a.eventOverlap,
                allow: a.eventAllow,
                classNames: a.eventClassNames,
                backgroundColor: a.eventBackgroundColor,
                borderColor: a.eventBorderColor,
                textColor: a.eventTextColor,
                color: a.eventColor
            }, o),
            extendedProps: r(r({}, s), a.extendedProps)
        };
        if (Object.freeze(l.ui.classNames),
        Object.freeze(l.extendedProps),
        n[l.id])
            ;
        else if (n[l.id] = l,
        a.children)
            for (var u = 0, c = a.children; u < c.length; u++) {
                bd(c[u], l.id, n, o)
            }
        return l
    }
    function Cd(e) {
        return 0 === e.indexOf(Sd) ? "" : e
    }
    function Dd(e, t, n, o) {
        if (!e || !t)
            return {};
        switch (t.type) {
        case "RECEIVE_RESOURCES":
            return function(e, t, n, r, o) {
                if (r.latestFetchId === n) {
                    for (var i = {}, a = 0, s = t; a < s.length; a++) {
                        bd(s[a], "", i, o)
                    }
                    return i
                }
                return e
            }(e, t.rawResources, t.fetchId, n, o);
        case "ADD_RESOURCE":
            return i = e,
            a = t.resourceHash,
            r(r({}, i), a);
        case "REMOVE_RESOURCE":
            return function(e, t) {
                var n = r({}, e);
                for (var o in delete n[t],
                n)
                    n[o].parentId === t && (n[o] = r(r({}, n[o]), {
                        parentId: ""
                    }));
                return n
            }(e, t.resourceId);
        case "SET_RESOURCE_PROP":
            return function(e, t, n, o) {
                var i, a, s = e[t];
                if (s)
                    return r(r({}, e), ((i = {})[t] = r(r({}, s), ((a = {})[n] = o,
                    a)),
                    i));
                return e
            }(e, t.resourceId, t.propName, t.propValue);
        case "SET_RESOURCE_EXTENDED_PROP":
            return function(e, t, n, o) {
                var i, a, s = e[t];
                if (s)
                    return r(r({}, e), ((i = {})[t] = r(r({}, s), {
                        extendedProps: r(r({}, s.extendedProps), (a = {},
                        a[n] = o,
                        a))
                    }),
                    i));
                return e
            }(e, t.resourceId, t.propName, t.propValue);
        default:
            return e
        }
        var i, a
    }
    var Rd = {
        resourceId: String,
        resourceIds: Wn,
        resourceEditable: Boolean
    };
    var wd = function() {
        function e(e, t) {
            this._context = e,
            this._resource = t
        }
        return e.prototype.setProp = function(e, t) {
            var n = this._resource;
            this._context.dispatch({
                type: "SET_RESOURCE_PROP",
                resourceId: n.id,
                propName: e,
                propValue: t
            }),
            this.sync(n)
        }
        ,
        e.prototype.setExtendedProp = function(e, t) {
            var n = this._resource;
            this._context.dispatch({
                type: "SET_RESOURCE_EXTENDED_PROP",
                resourceId: n.id,
                propName: e,
                propValue: t
            }),
            this.sync(n)
        }
        ,
        e.prototype.sync = function(t) {
            var n = this._context
              , r = t.id;
            this._resource = n.getCurrentData().resourceStore[r],
            n.emitter.trigger("resourceChange", {
                oldResource: new e(n,t),
                resource: this,
                revert: function() {
                    var e;
                    n.dispatch({
                        type: "ADD_RESOURCE",
                        resourceHash: (e = {},
                        e[r] = t,
                        e)
                    })
                }
            })
        }
        ,
        e.prototype.remove = function() {
            var e = this._context
              , t = this._resource
              , n = t.id;
            e.dispatch({
                type: "REMOVE_RESOURCE",
                resourceId: n
            }),
            e.emitter.trigger("resourceRemove", {
                resource: this,
                revert: function() {
                    var r;
                    e.dispatch({
                        type: "ADD_RESOURCE",
                        resourceHash: (r = {},
                        r[n] = t,
                        r)
                    })
                }
            })
        }
        ,
        e.prototype.getParent = function() {
            var t = this._context
              , n = this._resource.parentId;
            return n ? new e(t,t.getCurrentData().resourceSource[n]) : null
        }
        ,
        e.prototype.getChildren = function() {
            var t = this._resource.id
              , n = this._context
              , r = n.getCurrentData().resourceStore
              , o = [];
            for (var i in r)
                r[i].parentId === t && o.push(new e(n,r[i]));
            return o
        }
        ,
        e.prototype.getEvents = function() {
            var e = this._resource.id
              , t = this._context
              , n = t.getCurrentData().eventStore
              , r = n.defs
              , o = n.instances
              , i = [];
            for (var a in o) {
                var s = o[a]
                  , l = r[s.defId];
                -1 !== l.resourceIds.indexOf(e) && i.push(new Zr(t,l,s))
            }
            return i
        }
        ,
        Object.defineProperty(e.prototype, "id", {
            get: function() {
                return Cd(this._resource.id)
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "title", {
            get: function() {
                return this._resource.title
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventConstraint", {
            get: function() {
                return this._resource.ui.constraints[0] || null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventOverlap", {
            get: function() {
                return this._resource.ui.overlap
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventAllow", {
            get: function() {
                return this._resource.ui.allows[0] || null
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventBackgroundColor", {
            get: function() {
                return this._resource.ui.backgroundColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventBorderColor", {
            get: function() {
                return this._resource.ui.borderColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventTextColor", {
            get: function() {
                return this._resource.ui.textColor
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "eventClassNames", {
            get: function() {
                return this._resource.ui.classNames
            },
            enumerable: !1,
            configurable: !0
        }),
        Object.defineProperty(e.prototype, "extendedProps", {
            get: function() {
                return this._resource.extendedProps
            },
            enumerable: !1,
            configurable: !0
        }),
        e.prototype.toPlainObject = function(e) {
            void 0 === e && (e = {});
            var t = this._resource
              , n = t.ui
              , o = this.id
              , i = {};
            return o && (i.id = o),
            t.title && (i.title = t.title),
            e.collapseEventColor && n.backgroundColor && n.backgroundColor === n.borderColor ? i.eventColor = n.backgroundColor : (n.backgroundColor && (i.eventBackgroundColor = n.backgroundColor),
            n.borderColor && (i.eventBorderColor = n.borderColor)),
            n.textColor && (i.eventTextColor = n.textColor),
            n.classNames.length && (i.eventClassNames = n.classNames),
            Object.keys(t.extendedProps).length && (e.collapseExtendedProps ? r(i, t.extendedProps) : i.extendedProps = t.extendedProps),
            i
        }
        ,
        e.prototype.toJSON = function() {
            return this.toPlainObject()
        }
        ,
        e
    }();
    Yr.prototype.addResource = function(e, t) {
        var n, r = this;
        void 0 === t && (t = !0);
        var o, i, a = this.getCurrentData();
        e instanceof wd ? ((n = {})[(i = e._resource).id] = i,
        o = n) : i = bd(e, "", o = {}, a),
        this.dispatch({
            type: "ADD_RESOURCE",
            resourceHash: o
        }),
        t && this.trigger("_scrollRequest", {
            resourceId: i.id
        });
        var s = new wd(a,i);
        return a.emitter.trigger("resourceAdd", {
            resource: s,
            revert: function() {
                r.dispatch({
                    type: "REMOVE_RESOURCE",
                    resourceId: i.id
                })
            }
        }),
        s
    }
    ,
    Yr.prototype.getResourceById = function(e) {
        e = String(e);
        var t = this.getCurrentData();
        if (t.resourceStore) {
            var n = t.resourceStore[e];
            if (n)
                return new wd(t,n)
        }
        return null
    }
    ,
    Yr.prototype.getResources = function() {
        var e = this.getCurrentData()
          , t = e.resourceStore
          , n = [];
        if (t)
            for (var r in t)
                n.push(new wd(e,t[r]));
        return n
    }
    ,
    Yr.prototype.getTopLevelResources = function() {
        var e = this.getCurrentData()
          , t = e.resourceStore
          , n = [];
        if (t)
            for (var r in t)
                t[r].parentId || n.push(new wd(e,t[r]));
        return n
    }
    ,
    Yr.prototype.refetchResources = function() {
        this.dispatch({
            type: "REFETCH_RESOURCES"
        })
    }
    ;
    var Td = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.getKeyInfo = function(e) {
            return r({
                "": {}
            }, e.resourceStore)
        }
        ,
        t.prototype.getKeysForDateSpan = function(e) {
            return [e.resourceId || ""]
        }
        ,
        t.prototype.getKeysForEventDef = function(e) {
            var t = e.resourceIds;
            return t.length ? t : [""]
        }
        ,
        t
    }(Co);
    function _d(e, t) {
        return r(r({}, t), {
            constraints: xd(e, t.constraints)
        })
    }
    function xd(e, t) {
        return t.map((function(t) {
            var n = t.defs;
            if (n)
                for (var r in n) {
                    var o = n[r].resourceIds;
                    if (o.length && -1 === o.indexOf(e))
                        return !1
                }
            return t
        }
        ))
    }
    Zr.prototype.getResources = function() {
        var e = this._context.calendarApi;
        return this._def.resourceIds.map((function(t) {
            return e.getResourceById(t)
        }
        ))
    }
    ,
    Zr.prototype.setResources = function(e) {
        for (var t = [], n = 0, r = e; n < r.length; n++) {
            var o = r[n]
              , i = null;
            "string" == typeof o ? i = o : "number" == typeof o ? i = String(o) : o instanceof wd ? i = o.id : console.warn("unknown resource type: " + o),
            i && t.push(i)
        }
        this.mutate({
            standardProps: {
                resourceIds: t
            }
        })
    }
    ;
    var kd = {
        resources: function(e, t) {
            t.getCurrentData().resourceSource._raw !== e && t.dispatch({
                type: "RESET_RESOURCE_SOURCE",
                resourceSourceInput: e
            })
        }
    };
    var Md = rt("id,title");
    var Id = {
        initialResources: Wn,
        resources: Wn,
        eventResourceEditable: Boolean,
        refetchResourcesOnNavigate: Boolean,
        resourceOrder: rt,
        filterResourcesWithEvents: Boolean,
        resourceGroupField: String,
        resourceAreaWidth: Wn,
        resourceAreaColumns: Wn,
        resourcesInitiallyExpanded: Boolean,
        datesAboveResources: Boolean,
        needsResourceData: Boolean,
        resourceAreaHeaderClassNames: Wn,
        resourceAreaHeaderContent: Wn,
        resourceAreaHeaderDidMount: Wn,
        resourceAreaHeaderWillUnmount: Wn,
        resourceGroupLabelClassNames: Wn,
        resourceGroupLabelContent: Wn,
        resourceGroupLabelDidMount: Wn,
        resourceGroupLabelWillUnmount: Wn,
        resourceLabelClassNames: Wn,
        resourceLabelContent: Wn,
        resourceLabelDidMount: Wn,
        resourceLabelWillUnmount: Wn,
        resourceLaneClassNames: Wn,
        resourceLaneContent: Wn,
        resourceLaneDidMount: Wn,
        resourceLaneWillUnmount: Wn,
        resourceGroupLaneClassNames: Wn,
        resourceGroupLaneContent: Wn,
        resourceGroupLaneDidMount: Wn,
        resourceGroupLaneWillUnmount: Wn
    }
      , Pd = {
        resourcesSet: Wn,
        resourceAdd: Wn,
        resourceChange: Wn,
        resourceRemove: Wn
    };
    function Nd(e) {
        return Yo(ni.Consumer, null, (function(t) {
            var n = t.options
              , r = {
                resource: new wd(t,e.resource),
                date: e.date ? t.dateEnv.toDate(e.date) : null,
                view: t.viewApi
            }
              , o = {
                "data-resource-id": e.resource.id,
                "data-date": e.date ? on(e.date) : void 0
            };
            return Yo(hi, {
                hookProps: r,
                classNames: n.resourceLabelClassNames,
                content: n.resourceLabelContent,
                defaultContent: Hd,
                didMount: n.resourceLabelDidMount,
                willUnmount: n.resourceLabelWillUnmount
            }, (function(t, n, r, i) {
                return e.children(t, n, o, r, i)
            }
            ))
        }
        ))
    }
    function Hd(e) {
        return e.resource.title || e.resource.id
    }
    dd({
        ignoreRange: !0,
        parseMeta: function(e) {
            return Array.isArray(e.resources) ? e.resources : null
        },
        fetch: function(e, t) {
            t({
                rawResources: e.resourceSource.meta
            })
        }
    }),
    dd({
        parseMeta: function(e) {
            return "function" == typeof e.resources ? e.resources : null
        },
        fetch: function(e, t, n) {
            var r = e.context.dateEnv
              , o = e.resourceSource.meta
              , i = e.range ? {
                start: r.toDate(e.range.start),
                end: r.toDate(e.range.end),
                startStr: r.formatIso(e.range.start),
                endStr: r.formatIso(e.range.end),
                timeZone: r.timeZone
            } : {};
            Uo(o.bind(null, i), (function(e) {
                t({
                    rawResources: e
                })
            }
            ), n)
        }
    }),
    dd({
        parseMeta: function(e) {
            return e.url ? {
                url: e.url,
                method: (e.method || "GET").toUpperCase(),
                extraParams: e.extraParams
            } : null
        },
        fetch: function(e, t, n) {
            var o = e.resourceSource.meta
              , i = function(e, t, n) {
                var o, i, a, s, l = n.dateEnv, u = n.options, c = {};
                t && (null == (o = e.startParam) && (o = u.startParam),
                null == (i = e.endParam) && (i = u.endParam),
                null == (a = e.timeZoneParam) && (a = u.timeZoneParam),
                c[o] = l.formatIso(t.start),
                c[i] = l.formatIso(t.end),
                "local" !== l.timeZone && (c[a] = l.timeZone));
                s = "function" == typeof e.extraParams ? e.extraParams() : e.extraParams || {};
                return r(c, s),
                c
            }(o, e.range, e.context);
            Yi(o.method, o.url, i, (function(e, n) {
                t({
                    rawResources: e,
                    xhr: n
                })
            }
            ), (function(e, t) {
                n({
                    message: e,
                    xhr: t
                })
            }
            ))
        }
    });
    var Od = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props;
            return Yo(Nd, {
                resource: e.resource,
                date: e.date
            }, (function(t, n, o, i, a) {
                return Yo("th", r({
                    ref: t,
                    role: "columnheader",
                    className: ["fc-col-header-cell", "fc-resource"].concat(n).join(" "),
                    colSpan: e.colSpan
                }, o), Yo("div", {
                    className: "fc-scrollgrid-sync-inner"
                }, Yo("span", {
                    className: ["fc-col-header-cell-cushion", e.isSticky ? "fc-sticky" : ""].join(" "),
                    ref: i
                }, a)))
            }
            ))
        }
        ,
        t
    }(ii)
      , Ad = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildDateFormat = cn(Wd),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = this.buildDateFormat(n.options.dayHeaderFormat, t.datesRepDistinctDays, t.dates.length);
            return Yo(Ga, {
                unit: "day"
            }, (function(o, i) {
                return 1 === t.dates.length ? e.renderResourceRow(t.resources, t.dates[0]) : n.options.datesAboveResources ? e.renderDayAndResourceRows(t.dates, r, i, t.resources) : e.renderResourceAndDayRows(t.resources, t.dates, r, i)
            }
            ))
        }
        ,
        t.prototype.renderResourceRow = function(e, t) {
            var n = e.map((function(e) {
                return Yo(Od, {
                    key: e.id,
                    resource: e,
                    colSpan: 1,
                    date: t
                })
            }
            ));
            return this.buildTr(n, "resources")
        }
        ,
        t.prototype.renderDayAndResourceRows = function(e, t, n, r) {
            for (var o = [], i = [], a = 0, s = e; a < s.length; a++) {
                var l = s[a];
                o.push(this.renderDateCell(l, t, n, r.length, null, !0));
                for (var u = 0, c = r; u < c.length; u++) {
                    var d = c[u];
                    i.push(Yo(Od, {
                        key: d.id + ":" + l.toISOString(),
                        resource: d,
                        colSpan: 1,
                        date: l
                    }))
                }
            }
            return Yo(Ko, null, this.buildTr(o, "day"), this.buildTr(i, "resources"))
        }
        ,
        t.prototype.renderResourceAndDayRows = function(e, t, n, r) {
            for (var o = [], i = [], a = 0, s = e; a < s.length; a++) {
                var l = s[a];
                o.push(Yo(Od, {
                    key: l.id,
                    resource: l,
                    colSpan: t.length,
                    isSticky: !0
                }));
                for (var u = 0, c = t; u < c.length; u++) {
                    var d = c[u];
                    i.push(this.renderDateCell(d, n, r, 1, l))
                }
            }
            return Yo(Ko, null, this.buildTr(o, "resources"), this.buildTr(i, "day"))
        }
        ,
        t.prototype.renderDateCell = function(e, t, n, r, o, i) {
            var a = this.props
              , s = o ? ":" + o.id : ""
              , l = o ? {
                resource: new wd(this.context,o)
            } : {}
              , u = o ? {
                "data-resource-id": o.id
            } : {};
            return a.datesRepDistinctDays ? Yo(za, {
                key: e.toISOString() + s,
                date: e,
                dateProfile: a.dateProfile,
                todayRange: n,
                colCnt: a.dates.length * a.resources.length,
                dayHeaderFormat: t,
                colSpan: r,
                isSticky: i,
                extraHookProps: l,
                extraDataAttrs: u
            }) : Yo(Fa, {
                key: e.getUTCDay() + s,
                dow: e.getUTCDay(),
                dayHeaderFormat: t,
                colSpan: r,
                isSticky: i,
                extraHookProps: l,
                extraDataAttrs: u
            })
        }
        ,
        t.prototype.buildTr = function(e, t) {
            var n = this.props.renderIntro;
            return e.length || (e = [Yo("td", {
                key: 0
            }, " ")]),
            Yo("tr", {
                key: t,
                role: "row"
            }, n && n(t), e)
        }
        ,
        t
    }(ii);
    function Wd(e, t, n) {
        return e || La(t, n)
    }
    var Ld = function(e) {
        for (var t = {}, n = [], r = 0; r < e.length; r += 1) {
            var o = e[r].id;
            n.push(o),
            t[o] = r
        }
        this.ids = n,
        this.indicesById = t,
        this.length = e.length
    }
      , Ud = function() {
        function e(e, t, n) {
            this.dayTableModel = e,
            this.resources = t,
            this.context = n,
            this.resourceIndex = new Ld(t),
            this.rowCnt = e.rowCnt,
            this.colCnt = e.colCnt * t.length,
            this.cells = this.buildCells()
        }
        return e.prototype.buildCells = function() {
            for (var e = this, t = e.rowCnt, n = e.dayTableModel, r = e.resources, o = [], i = 0; i < t; i += 1) {
                for (var a = [], s = 0; s < n.colCnt; s += 1)
                    for (var l = 0; l < r.length; l += 1) {
                        var u = r[l]
                          , c = {
                            resource: new wd(this.context,u)
                        }
                          , d = {
                            "data-resource-id": u.id
                        }
                          , p = {
                            resourceId: u.id
                        }
                          , f = n.cells[i][s].date;
                        a[this.computeCol(s, l)] = {
                            key: u.id + ":" + f.toISOString(),
                            date: f,
                            extraHookProps: c,
                            extraDataAttrs: d,
                            extraClassNames: ["fc-resource"],
                            extraDateSpan: p
                        }
                    }
                o.push(a)
            }
            return o
        }
        ,
        e
    }()
      , Bd = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.computeCol = function(e, t) {
            return t * this.dayTableModel.colCnt + e
        }
        ,
        t.prototype.computeColRanges = function(e, t, n) {
            return [{
                firstCol: this.computeCol(e, n),
                lastCol: this.computeCol(t, n),
                isStart: !0,
                isEnd: !0
            }]
        }
        ,
        t
    }(Ud)
      , zd = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.computeCol = function(e, t) {
            return e * this.resources.length + t
        }
        ,
        t.prototype.computeColRanges = function(e, t, n) {
            for (var r = [], o = e; o <= t; o += 1) {
                var i = this.computeCol(o, n);
                r.push({
                    firstCol: i,
                    lastCol: i,
                    isStart: o === e,
                    isEnd: o === t
                })
            }
            return r
        }
        ,
        t
    }(Ud)
      , Vd = []
      , Fd = function() {
        function e() {
            this.joinDateSelection = cn(this.joinSegs),
            this.joinBusinessHours = cn(this.joinSegs),
            this.joinFgEvents = cn(this.joinSegs),
            this.joinBgEvents = cn(this.joinSegs),
            this.joinEventDrags = cn(this.joinInteractions),
            this.joinEventResizes = cn(this.joinInteractions)
        }
        return e.prototype.joinProps = function(e, t) {
            for (var n = [], r = [], i = [], a = [], s = [], l = [], u = "", c = 0, d = t.resourceIndex.ids.concat([""]); c < d.length; c++) {
                var p = d[c]
                  , f = e[p];
                n.push(f.dateSelectionSegs),
                r.push(p ? f.businessHourSegs : Vd),
                i.push(p ? f.fgEventSegs : Vd),
                a.push(f.bgEventSegs),
                s.push(f.eventDrag),
                l.push(f.eventResize),
                u = u || f.eventSelection
            }
            return {
                dateSelectionSegs: this.joinDateSelection.apply(this, o([t], n)),
                businessHourSegs: this.joinBusinessHours.apply(this, o([t], r)),
                fgEventSegs: this.joinFgEvents.apply(this, o([t], i)),
                bgEventSegs: this.joinBgEvents.apply(this, o([t], a)),
                eventDrag: this.joinEventDrags.apply(this, o([t], s)),
                eventResize: this.joinEventResizes.apply(this, o([t], l)),
                eventSelection: u
            }
        }
        ,
        e.prototype.joinSegs = function(e) {
            for (var t = [], n = 1; n < arguments.length; n++)
                t[n - 1] = arguments[n];
            for (var r = e.resources.length, o = [], i = 0; i < r; i += 1) {
                for (var a = 0, s = t[i]; a < s.length; a++) {
                    var l = s[a];
                    o.push.apply(o, this.transformSeg(l, e, i))
                }
                for (var u = 0, c = t[r]; u < c.length; u++) {
                    l = c[u];
                    o.push.apply(o, this.transformSeg(l, e, i))
                }
            }
            return o
        }
        ,
        e.prototype.expandSegs = function(e, t) {
            for (var n = e.resources.length, r = [], o = 0; o < n; o += 1)
                for (var i = 0, a = t; i < a.length; i++) {
                    var s = a[i];
                    r.push.apply(r, this.transformSeg(s, e, o))
                }
            return r
        }
        ,
        e.prototype.joinInteractions = function(e) {
            for (var t = [], n = 1; n < arguments.length; n++)
                t[n - 1] = arguments[n];
            for (var o = e.resources.length, i = {}, a = [], s = !1, l = !1, u = 0; u < o; u += 1) {
                var c = t[u];
                if (c) {
                    s = !0;
                    for (var d = 0, p = c.segs; d < p.length; d++) {
                        var f = p[d];
                        a.push.apply(a, this.transformSeg(f, e, u))
                    }
                    r(i, c.affectedInstances),
                    l = l || c.isEvent
                }
                if (t[o])
                    for (var h = 0, v = t[o].segs; h < v.length; h++) {
                        f = v[h];
                        a.push.apply(a, this.transformSeg(f, e, u))
                    }
            }
            return s ? {
                affectedInstances: i,
                segs: a,
                isEvent: l
            } : null
        }
        ,
        e
    }()
      , Gd = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.getKeyInfo = function(e) {
            var t = e.resourceDayTableModel
              , n = Ht(t.resourceIndex.indicesById, (function(e) {
                return t.resources[e]
            }
            ));
            return n[""] = {},
            n
        }
        ,
        t.prototype.getKeysForDateSpan = function(e) {
            return [e.resourceId || ""]
        }
        ,
        t.prototype.getKeysForEventDef = function(e) {
            var t = e.resourceIds;
            return t.length ? t : [""]
        }
        ,
        t
    }(Co);
    function jd(e, t) {
        return qd(e, [], t, !1, {}, !0).map((function(e) {
            return e.resource
        }
        ))
    }
    function qd(e, t, n, r, o, i) {
        var a = [];
        return Yd(function(e, t, n, r) {
            var o = function(e, t) {
                var n = {};
                for (var r in e) {
                    var o = e[r];
                    n[r] = {
                        resource: o,
                        resourceFields: Kd(o),
                        children: []
                    }
                }
                for (var r in e) {
                    if ((o = e[r]).parentId) {
                        var i = n[o.parentId];
                        i && Xd(n[r], i.children, t)
                    }
                }
                return n
            }(e, r)
              , i = [];
            for (var a in o) {
                var s = o[a];
                s.resource.parentId || Zd(s, i, n, 0, t, r)
            }
            return i
        }(e, r ? -1 : 1, t, n), a, r, [], 0, o, i),
        a
    }
    function Yd(e, t, n, r, o, i, a) {
        for (var s = 0; s < e.length; s += 1) {
            var l = e[s]
              , u = l.group;
            if (u)
                if (n) {
                    var c = t.length
                      , d = r.length;
                    if (Yd(l.children, t, n, r.concat(0), o, i, a),
                    c < t.length) {
                        var p = t[c];
                        (p.rowSpans = p.rowSpans.slice())[d] = t.length - c
                    }
                } else {
                    var f = null != i[h = u.spec.field + ":" + u.value] ? i[h] : a;
                    t.push({
                        id: h,
                        group: u,
                        isExpanded: f
                    }),
                    f && Yd(l.children, t, n, r, o + 1, i, a)
                }
            else if (l.resource) {
                var h;
                f = null != i[h = l.resource.id] ? i[h] : a;
                t.push({
                    id: h,
                    rowSpans: r,
                    depth: o,
                    isExpanded: f,
                    hasChildren: Boolean(l.children.length),
                    resource: l.resource,
                    resourceFields: l.resourceFields
                }),
                f && Yd(l.children, t, n, r, o + 1, i, a)
            }
        }
    }
    function Zd(e, t, n, r, o, i) {
        n.length && (-1 === o || r <= o) ? Zd(e, function(e, t, n) {
            var r, o, i = e.resourceFields[n.field];
            if (n.order)
                for (o = 0; o < t.length; o += 1) {
                    if ((s = t[o]).group) {
                        var a = at(i, s.group.value) * n.order;
                        if (0 === a) {
                            r = s;
                            break
                        }
                        if (a < 0)
                            break
                    }
                }
            else
                for (o = 0; o < t.length; o += 1) {
                    var s;
                    if ((s = t[o]).group && i === s.group.value) {
                        r = s;
                        break
                    }
                }
            r || (r = {
                group: {
                    value: i,
                    spec: n
                },
                children: []
            },
            t.splice(o, 0, r));
            return r
        }(e, t, n[0]).children, n.slice(1), r + 1, o, i) : Xd(e, t, i)
    }
    function Xd(e, t, n) {
        var r;
        for (r = 0; r < t.length; r += 1) {
            if (ot(t[r].resourceFields, e.resourceFields, n) > 0)
                break
        }
        t.splice(r, 0, e)
    }
    function Kd(e) {
        var t = r(r(r({}, e.extendedProps), e.ui), e);
        return delete t.ui,
        delete t.extendedProps,
        t
    }
    function $d(e, t) {
        return e.spec === t.spec && e.value === t.value
    }
    var Jd = ci({
        deps: [Xu],
        reducers: [function(e, t, n) {
            var o = gd(e && e.resourceSource, t, n);
            return {
                resourceSource: o,
                resourceStore: Dd(e && e.resourceStore, t, o, n),
                resourceEntityExpansions: function(e, t) {
                    var n;
                    if (!e || !t)
                        return {};
                    switch (t.type) {
                    case "SET_RESOURCE_ENTITY_EXPANDED":
                        return r(r({}, e), ((n = {})[t.id] = t.isExpanded,
                        n));
                    default:
                        return e
                    }
                }(e && e.resourceEntityExpansions, t)
            }
        }
        ],
        isLoadingFuncs: [function(e) {
            return e.resourceSource && e.resourceSource.isFetching
        }
        ],
        eventRefiners: Rd,
        eventDefMemberAdders: [function(e) {
            return {
                resourceIds: (t = e.resourceIds,
                (t || []).map((function(e) {
                    return String(e)
                }
                ))).concat(e.resourceId ? [e.resourceId] : []),
                resourceEditable: e.resourceEditable
            };
            var t
        }
        ],
        isDraggableTransformers: [function(e, t, n, r) {
            if (!e) {
                var o = r.getCurrentData();
                if (o.viewSpecs[o.currentViewType].optionDefaults.needsResourceData && od(t, r))
                    return !0
            }
            return e
        }
        ],
        eventDragMutationMassagers: [function(e, t, n) {
            var r = t.dateSpan.resourceId
              , o = n.dateSpan.resourceId;
            r && o && r !== o && (e.resourceMutation = {
                matchResourceId: r,
                setResourceId: o
            })
        }
        ],
        eventDefMutationAppliers: [function(e, t, n) {
            var r = t.resourceMutation;
            if (r && od(e, n)) {
                var o = e.resourceIds.indexOf(r.matchResourceId);
                if (-1 !== o) {
                    var i = e.resourceIds.slice();
                    i.splice(o, 1),
                    -1 === i.indexOf(r.setResourceId) && i.push(r.setResourceId),
                    e.resourceIds = i
                }
            }
        }
        ],
        dateSelectionTransformers: [function(e, t) {
            var n = e.dateSpan.resourceId
              , r = t.dateSpan.resourceId;
            return n && r ? {
                resourceId: n
            } : null
        }
        ],
        datePointTransforms: [function(e, t) {
            return e.resourceId ? {
                resource: t.calendarApi.getResourceById(e.resourceId)
            } : {}
        }
        ],
        dateSpanTransforms: [function(e, t) {
            return e.resourceId ? {
                resource: t.calendarApi.getResourceById(e.resourceId)
            } : {}
        }
        ],
        viewPropsTransformers: [id, sd],
        isPropsValid: function(e, t) {
            var n = (new Td).splitProps(r(r({}, e), {
                resourceStore: t.getCurrentData().resourceStore
            }));
            for (var o in n) {
                var i = n[o];
                if (o && n[""] && (i = r(r({}, i), {
                    eventStore: Vn(n[""].eventStore, i.eventStore),
                    eventUiBases: r(r({}, n[""].eventUiBases), i.eventUiBases)
                })),
                !ts(i, t, {
                    resourceId: o
                }, _d.bind(null, o)))
                    return !1
            }
            return !0
        },
        externalDefTransforms: [function(e) {
            return e.resourceId ? {
                resourceId: e.resourceId
            } : {}
        }
        ],
        eventDropTransformers: [function(e, t) {
            var n = e.resourceMutation;
            if (n) {
                var r = t.calendarApi;
                return {
                    oldResource: r.getResourceById(n.matchResourceId),
                    newResource: r.getResourceById(n.setResourceId)
                }
            }
            return {
                oldResource: null,
                newResource: null
            }
        }
        ],
        optionChangeHandlers: kd,
        optionRefiners: Id,
        listenerRefiners: Pd,
        propSetHandlers: {
            resourceStore: function(e, t) {
                var n = t.emitter;
                n.hasHandlers("resourcesSet") && n.trigger("resourcesSet", function(e, t) {
                    var n = [];
                    for (var r in e)
                        n.push(new wd(t,e[r]));
                    return n
                }(e, t))
            }
        }
    })
      , Qd = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.transformSeg = function(e, t, n) {
            return t.computeColRanges(e.firstCol, e.lastCol, n).map((function(t) {
                return r(r(r({}, e), t), {
                    isStart: e.isStart && t.isStart,
                    isEnd: e.isEnd && t.isEnd
                })
            }
            ))
        }
        ,
        t
    }(Fd)
      , ep = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.splitter = new Gd,
            t.slicers = {},
            t.joiner = new Qd,
            t.tableRef = Xo(),
            t.isHitComboAllowed = function(e, n) {
                return 1 === t.props.resourceDayTableModel.dayTableModel.colCnt || e.dateSpan.resourceId === n.dateSpan.resourceId
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = t.resourceDayTableModel
              , i = t.nextDayThreshold
              , a = t.dateProfile
              , s = this.splitter.splitProps(t);
            this.slicers = Ht(s, (function(t, n) {
                return e.slicers[n] || new Bl
            }
            ));
            var l = Ht(this.slicers, (function(e, t) {
                return e.sliceProps(s[t], a, i, n, o.dayTableModel)
            }
            ));
            return Yo(Ll, r({
                forPrint: t.forPrint,
                ref: this.tableRef
            }, this.joiner.joinProps(l, o), {
                cells: o.cells,
                dateProfile: a,
                colGroupNode: t.colGroupNode,
                tableMinWidth: t.tableMinWidth,
                renderRowIntro: t.renderRowIntro,
                dayMaxEvents: t.dayMaxEvents,
                dayMaxEventRows: t.dayMaxEventRows,
                showWeekNumbers: t.showWeekNumbers,
                expandRows: t.expandRows,
                headerAlignElRef: t.headerAlignElRef,
                clientWidth: t.clientWidth,
                clientHeight: t.clientHeight,
                isHitComboAllowed: this.isHitComboAllowed
            }))
        }
        ,
        t
    }(ui)
      , tp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.flattenResources = cn(jd),
            t.buildResourceDayTableModel = cn(np),
            t.headerRef = Xo(),
            t.tableRef = Xo(),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = n.options
              , o = r.resourceOrder || Md
              , i = this.flattenResources(t.resourceStore, o)
              , a = this.buildResourceDayTableModel(t.dateProfile, n.dateProfileGenerator, i, r.datesAboveResources, n)
              , s = r.dayHeaders && Yo(Ad, {
                ref: this.headerRef,
                resources: i,
                dateProfile: t.dateProfile,
                dates: a.dayTableModel.headerDates,
                datesRepDistinctDays: !0
            })
              , l = function(n) {
                return Yo(ep, {
                    ref: e.tableRef,
                    dateProfile: t.dateProfile,
                    resourceDayTableModel: a,
                    businessHours: t.businessHours,
                    eventStore: t.eventStore,
                    eventUiBases: t.eventUiBases,
                    dateSelection: t.dateSelection,
                    eventSelection: t.eventSelection,
                    eventDrag: t.eventDrag,
                    eventResize: t.eventResize,
                    nextDayThreshold: r.nextDayThreshold,
                    tableMinWidth: n.tableMinWidth,
                    colGroupNode: n.tableColGroupNode,
                    dayMaxEvents: r.dayMaxEvents,
                    dayMaxEventRows: r.dayMaxEventRows,
                    showWeekNumbers: r.weekNumbers,
                    expandRows: !t.isHeightAuto,
                    headerAlignElRef: e.headerElRef,
                    clientWidth: n.clientWidth,
                    clientHeight: n.clientHeight,
                    forPrint: t.forPrint
                })
            };
            return r.dayMinWidth ? this.renderHScrollLayout(s, l, a.colCnt, r.dayMinWidth) : this.renderSimpleLayout(s, l)
        }
        ,
        t
    }(Sl);
    function np(e, t, n, r, o) {
        var i = Fl(e, t);
        return r ? new zd(i,n,o) : new Bd(i,n,o)
    }
    var rp = ci({
        deps: [Xu, Jd, Gl],
        initialView: "resourceDayGridDay",
        views: {
            resourceDayGrid: {
                type: "dayGrid",
                component: tp,
                needsResourceData: !0
            },
            resourceDayGridDay: {
                type: "resourceDayGrid",
                duration: {
                    days: 1
                }
            },
            resourceDayGridWeek: {
                type: "resourceDayGrid",
                duration: {
                    weeks: 1
                }
            },
            resourceDayGridMonth: {
                type: "resourceDayGrid",
                duration: {
                    months: 1
                },
                monthMode: !0,
                fixedWeekCount: !0
            }
        }
    })
      , op = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.transformSeg = function(e, t, n) {
            return [r(r({}, e), {
                col: t.computeCol(e.col, n)
            })]
        }
        ,
        t
    }(Fd)
      , ip = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.buildDayRanges = cn(Du),
            t.splitter = new Gd,
            t.slicers = {},
            t.joiner = new op,
            t.timeColsRef = Xo(),
            t.isHitComboAllowed = function(e, n) {
                return 1 === t.dayRanges.length || e.dateSpan.resourceId === n.dateSpan.resourceId
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = n.dateEnv
              , i = n.options
              , a = t.dateProfile
              , s = t.resourceDayTableModel
              , l = this.dayRanges = this.buildDayRanges(s.dayTableModel, a, o)
              , u = this.splitter.splitProps(t);
            this.slicers = Ht(u, (function(t, n) {
                return e.slicers[n] || new bu
            }
            ));
            var c = Ht(this.slicers, (function(e, t) {
                return e.sliceProps(u[t], a, null, n, l)
            }
            ));
            return Yo(Ga, {
                unit: i.nowIndicator ? "minute" : "day"
            }, (function(n, o) {
                return Yo(Su, r({
                    ref: e.timeColsRef
                }, e.joiner.joinProps(c, s), {
                    dateProfile: a,
                    axis: t.axis,
                    slotDuration: t.slotDuration,
                    slatMetas: t.slatMetas,
                    cells: s.cells[0],
                    tableColGroupNode: t.tableColGroupNode,
                    tableMinWidth: t.tableMinWidth,
                    clientWidth: t.clientWidth,
                    clientHeight: t.clientHeight,
                    expandRows: t.expandRows,
                    nowDate: n,
                    nowIndicatorSegs: i.nowIndicator && e.buildNowIndicatorSegs(n),
                    todayRange: o,
                    onScrollTopRequest: t.onScrollTopRequest,
                    forPrint: t.forPrint,
                    onSlatCoords: t.onSlatCoords,
                    isHitComboAllowed: e.isHitComboAllowed
                }))
            }
            ))
        }
        ,
        t.prototype.buildNowIndicatorSegs = function(e) {
            var t = this.slicers[""].sliceNowDate(e, this.context, this.dayRanges);
            return this.joiner.expandSegs(this.props.resourceDayTableModel, t)
        }
        ,
        t
    }(ui)
      , ap = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.flattenResources = cn(jd),
            t.buildResourceTimeColsModel = cn(sp),
            t.buildSlatMetas = cn(wu),
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , o = n.options
              , i = n.dateEnv
              , a = t.dateProfile
              , s = this.allDaySplitter.splitProps(t)
              , l = o.resourceOrder || Md
              , u = this.flattenResources(t.resourceStore, l)
              , c = this.buildResourceTimeColsModel(a, n.dateProfileGenerator, u, o.datesAboveResources, n)
              , d = this.buildSlatMetas(a.slotMinTime, a.slotMaxTime, o.slotLabelInterval, o.slotDuration, i)
              , p = o.dayMinWidth
              , f = !p
              , h = p
              , v = o.dayHeaders && Yo(Ad, {
                resources: u,
                dates: c.dayTableModel.headerDates,
                dateProfile: a,
                datesRepDistinctDays: !0,
                renderIntro: f ? this.renderHeadAxis : null
            })
              , g = !1 !== o.allDaySlot && function(n) {
                return Yo(ep, r({}, s.allDay, {
                    dateProfile: a,
                    resourceDayTableModel: c,
                    nextDayThreshold: o.nextDayThreshold,
                    tableMinWidth: n.tableMinWidth,
                    colGroupNode: n.tableColGroupNode,
                    renderRowIntro: f ? e.renderTableRowAxis : null,
                    showWeekNumbers: !1,
                    expandRows: !1,
                    headerAlignElRef: e.headerElRef,
                    clientWidth: n.clientWidth,
                    clientHeight: n.clientHeight,
                    forPrint: t.forPrint
                }, e.getAllDayMaxEventProps()))
            }
              , m = function(n) {
                return Yo(ip, r({}, s.timed, {
                    dateProfile: a,
                    axis: f,
                    slotDuration: o.slotDuration,
                    slatMetas: d,
                    resourceDayTableModel: c,
                    tableColGroupNode: n.tableColGroupNode,
                    tableMinWidth: n.tableMinWidth,
                    clientWidth: n.clientWidth,
                    clientHeight: n.clientHeight,
                    onSlatCoords: e.handleSlatCoords,
                    expandRows: n.expandRows,
                    forPrint: t.forPrint,
                    onScrollTopRequest: e.handleScrollTopRequest
                }))
            };
            return h ? this.renderHScrollLayout(v, g, m, c.colCnt, p, d, this.state.slatCoords) : this.renderSimpleLayout(v, g, m)
        }
        ,
        t
    }($l);
    function sp(e, t, n, r, o) {
        var i = _u(e, t);
        return r ? new zd(i,n,o) : new Bd(i,n,o)
    }
    var lp = ci({
        deps: [Xu, Jd, xu],
        initialView: "resourceTimeGridDay",
        views: {
            resourceTimeGrid: {
                type: "timeGrid",
                component: ap,
                needsResourceData: !0
            },
            resourceTimeGridDay: {
                type: "resourceTimeGrid",
                duration: {
                    days: 1
                }
            },
            resourceTimeGridWeek: {
                type: "resourceTimeGrid",
                duration: {
                    weeks: 1
                }
            }
        }
    });
    function up(e) {
        for (var t = e.depth, n = e.hasChildren, r = e.isExpanded, i = e.onExpanderClick, a = [], s = 0; s < t; s += 1)
            a.push(Yo("span", {
                className: "fc-icon"
            }));
        var l = ["fc-icon"];
        return n && (r ? l.push("fc-icon-minus-square") : l.push("fc-icon-plus-square")),
        a.push(Yo("span", {
            className: "fc-datagrid-expander" + (n ? "" : " fc-datagrid-expander-placeholder"),
            onClick: i
        }, Yo("span", {
            className: l.join(" ")
        }))),
        Yo.apply(void 0, o([Ko, {}], a))
    }
    function cp(e) {
        return {
            resource: new wd(e.context,e.resource),
            fieldValue: e.fieldValue,
            view: e.context.viewApi
        }
    }
    var dp = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props;
            return Yo(gi, {
                hookProps: e.hookProps,
                content: e.colSpec.cellContent,
                defaultContent: pp
            }, (function(e, t) {
                return Yo("span", {
                    className: "fc-datagrid-cell-main",
                    ref: e
                }, t)
            }
            ))
        }
        ,
        t
    }(ii);
    function pp(e) {
        return e.fieldValue || Yo(Ko, null, " ")
    }
    var fp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.refineHookProps = dn(cp),
            t.normalizeClassNames = Si(),
            t.onExpanderClick = function(e) {
                var n = t.props;
                n.hasChildren && t.context.dispatch({
                    type: "SET_RESOURCE_ENTITY_EXPANDED",
                    id: n.resource.id,
                    isExpanded: !n.isExpanded
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = t.colSpec
              , o = this.refineHookProps({
                resource: t.resource,
                fieldValue: t.fieldValue,
                context: n
            })
              , i = this.normalizeClassNames(r.cellClassNames, o);
            return Yo(yi, {
                hookProps: o,
                didMount: r.cellDidMount,
                willUnmount: r.cellWillUnmount
            }, (function(n) {
                return Yo("td", {
                    ref: n,
                    role: "gridcell",
                    "data-resource-id": t.resource.id,
                    className: ["fc-datagrid-cell", "fc-resource"].concat(i).join(" ")
                }, Yo("div", {
                    className: "fc-datagrid-cell-frame",
                    style: {
                        height: t.innerHeight
                    }
                }, Yo("div", {
                    className: "fc-datagrid-cell-cushion fc-scrollgrid-sync-inner"
                }, r.isMain && Yo(up, {
                    depth: t.depth,
                    hasChildren: t.hasChildren,
                    isExpanded: t.isExpanded,
                    onExpanderClick: e.onExpanderClick
                }), Yo(dp, {
                    hookProps: o,
                    colSpec: r
                }))))
            }
            ))
        }
        ,
        t
    }(ii)
      , hp = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = e.colSpec
              , r = {
                groupValue: e.fieldValue,
                view: t.viewApi
            };
            return Yo(hi, {
                hookProps: r,
                classNames: n.cellClassNames,
                content: n.cellContent,
                defaultContent: vp,
                didMount: n.cellDidMount,
                willUnmount: n.cellWillUnmount
            }, (function(t, n, r, o) {
                return Yo("td", {
                    ref: t,
                    role: "gridcell",
                    rowSpan: e.rowSpan,
                    className: ["fc-datagrid-cell", "fc-resource-group"].concat(n).join(" ")
                }, Yo("div", {
                    className: "fc-datagrid-cell-frame fc-datagrid-cell-frame-liquid"
                }, Yo("div", {
                    className: "fc-datagrid-cell-cushion fc-sticky",
                    ref: r
                }, o)))
            }
            ))
        }
        ,
        t
    }(ii);
    function vp(e) {
        return e.groupValue || Yo(Ko, null, " ")
    }
    var gp = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = e.resource
              , n = e.rowSpans
              , r = e.depth
              , o = Kd(t);
            return Yo("tr", {
                role: "row"
            }, e.colSpecs.map((function(i, a) {
                var s = n[a];
                if (0 === s)
                    return null;
                null == s && (s = 1);
                var l = i.field ? o[i.field] : t.title || Cd(t.id);
                return s > 1 ? Yo(hp, {
                    key: a,
                    colSpec: i,
                    fieldValue: l,
                    rowSpan: s
                }) : Yo(fp, {
                    key: a,
                    colSpec: i,
                    resource: t,
                    fieldValue: l,
                    depth: r,
                    hasChildren: e.hasChildren,
                    isExpanded: e.isExpanded,
                    innerHeight: e.innerHeight
                })
            }
            )))
        }
        ,
        t
    }(ii);
    gp.addPropsEquality({
        rowSpans: un
    });
    var mp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.innerInnerRef = Xo(),
            t.onExpanderClick = function() {
                var e = t.props;
                t.context.dispatch({
                    type: "SET_RESOURCE_ENTITY_EXPANDED",
                    id: e.id,
                    isExpanded: !e.isExpanded
                })
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = {
                groupValue: t.group.value,
                view: n.viewApi
            }
              , o = t.group.spec;
            return Yo("tr", {
                role: "row"
            }, Yo(hi, {
                hookProps: r,
                classNames: o.labelClassNames,
                content: o.labelContent,
                defaultContent: yp,
                didMount: o.labelDidMount,
                willUnmount: o.labelWillUnmount
            }, (function(r, o, i, a) {
                return Yo("th", {
                    ref: r,
                    role: "columnheader",
                    scope: "colgroup",
                    colSpan: t.spreadsheetColCnt,
                    className: ["fc-datagrid-cell", "fc-resource-group", n.theme.getClass("tableCellShaded")].concat(o).join(" ")
                }, Yo("div", {
                    className: "fc-datagrid-cell-frame",
                    style: {
                        height: t.innerHeight
                    }
                }, Yo("div", {
                    className: "fc-datagrid-cell-cushion fc-scrollgrid-sync-inner",
                    ref: e.innerInnerRef
                }, Yo(up, {
                    depth: 0,
                    hasChildren: !0,
                    isExpanded: t.isExpanded,
                    onExpanderClick: e.onExpanderClick
                }), Yo("span", {
                    className: "fc-datagrid-cell-main",
                    ref: i
                }, a))))
            }
            )))
        }
        ,
        t
    }(ii);
    function yp(e) {
        return e.groupValue || Yo(Ko, null, " ")
    }
    mp.addPropsEquality({
        group: $d
    });
    var Sp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.resizerElRefs = new ls(t._handleColResizerEl.bind(t)),
            t.colDraggings = {},
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = t.colSpecs
              , r = t.superHeaderRendering
              , o = t.rowInnerHeights
              , i = {
                view: this.context.viewApi
            }
              , a = [];
            if (o = o.slice(),
            r) {
                var s = o.shift();
                a.push(Yo("tr", {
                    key: "row-super",
                    role: "row"
                }, Yo(hi, {
                    hookProps: i,
                    classNames: r.headerClassNames,
                    content: r.headerContent,
                    didMount: r.headerDidMount,
                    willUnmount: r.headerWillUnmount
                }, (function(e, t, r, o) {
                    return Yo("th", {
                        ref: e,
                        role: "columnheader",
                        scope: "colgroup",
                        colSpan: n.length,
                        className: ["fc-datagrid-cell", "fc-datagrid-cell-super"].concat(t).join(" ")
                    }, Yo("div", {
                        className: "fc-datagrid-cell-frame",
                        style: {
                            height: s
                        }
                    }, Yo("div", {
                        className: "fc-datagrid-cell-cushion fc-scrollgrid-sync-inner",
                        ref: r
                    }, o)))
                }
                ))))
            }
            var l = o.shift();
            return a.push(Yo("tr", {
                key: "row",
                role: "row"
            }, n.map((function(t, r) {
                var o = r === n.length - 1;
                return Yo(hi, {
                    key: r,
                    hookProps: i,
                    classNames: t.headerClassNames,
                    content: t.headerContent,
                    didMount: t.headerDidMount,
                    willUnmount: t.headerWillUnmount
                }, (function(n, i, a, s) {
                    return Yo("th", {
                        ref: n,
                        role: "columnheader",
                        className: ["fc-datagrid-cell"].concat(i).join(" ")
                    }, Yo("div", {
                        className: "fc-datagrid-cell-frame",
                        style: {
                            height: l
                        }
                    }, Yo("div", {
                        className: "fc-datagrid-cell-cushion fc-scrollgrid-sync-inner"
                    }, t.isMain && Yo("span", {
                        className: "fc-datagrid-expander fc-datagrid-expander-placeholder"
                    }, Yo("span", {
                        className: "fc-icon"
                    })), Yo("span", {
                        className: "fc-datagrid-cell-main",
                        ref: a
                    }, s)), !o && Yo("div", {
                        className: "fc-datagrid-cell-resizer",
                        ref: e.resizerElRefs.createRef(r)
                    })))
                }
                ))
            }
            )))),
            Yo(Ko, null, a)
        }
        ,
        t.prototype._handleColResizerEl = function(e, t) {
            var n, r = this.colDraggings;
            e ? (n = this.initColResizing(e, parseInt(t, 10))) && (r[t] = n) : (n = r[t]) && (n.destroy(),
            delete r[t])
        }
        ,
        t.prototype.initColResizing = function(e, t) {
            var n = this.context
              , r = n.pluginHooks
              , o = n.isRtl
              , i = this.props.onColWidthChange
              , a = r.elementDraggingImpl;
            if (a) {
                var s, l, u = new a(e);
                return u.emitter.on("dragstart", (function() {
                    var n = He(Pe(e, "tr"), "th");
                    l = n.map((function(e) {
                        return e.getBoundingClientRect().width
                    }
                    )),
                    s = l[t]
                }
                )),
                u.emitter.on("dragmove", (function(e) {
                    l[t] = Math.max(s + e.deltaX * (o ? -1 : 1), 20),
                    i && i(l.slice())
                }
                )),
                u.setAutoScrollEnabled(!1),
                u
            }
            return null
        }
        ,
        t
    }(ii)
      , Ep = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = {
                resource: new wd(t,e.resource)
            };
            return Yo(gi, {
                hookProps: n,
                content: t.options.resourceLaneContent
            }, (function(e, t) {
                return t && Yo("div", {
                    className: "fc-timeline-lane-misc",
                    ref: e
                }, t)
            }
            ))
        }
        ,
        t
    }(ii)
      , bp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.refineHookProps = dn(Cp),
            t.normalizeClassNames = Si(),
            t.handleHeightChange = function(e, n) {
                t.props.onHeightChange && t.props.onHeightChange(Pe(e, "tr"), n)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.context
              , r = n.options
              , o = this.refineHookProps({
                resource: t.resource,
                context: n
            })
              , i = this.normalizeClassNames(r.resourceLaneClassNames, o);
            return Yo("tr", {
                ref: t.elRef
            }, Yo(yi, {
                hookProps: o,
                didMount: r.resourceLaneDidMount,
                willUnmount: r.resourceLaneWillUnmount
            }, (function(n) {
                return Yo("td", {
                    ref: n,
                    className: ["fc-timeline-lane", "fc-resource"].concat(i).join(" "),
                    "data-resource-id": t.resource.id
                }, Yo("div", {
                    className: "fc-timeline-lane-frame",
                    style: {
                        height: t.innerHeight
                    }
                }, Yo(Ep, {
                    resource: t.resource
                }), Yo(Qc, {
                    dateProfile: t.dateProfile,
                    tDateProfile: t.tDateProfile,
                    nowDate: t.nowDate,
                    todayRange: t.todayRange,
                    nextDayThreshold: t.nextDayThreshold,
                    businessHours: t.businessHours,
                    eventStore: t.eventStore,
                    eventUiBases: t.eventUiBases,
                    dateSelection: t.dateSelection,
                    eventSelection: t.eventSelection,
                    eventDrag: t.eventDrag,
                    eventResize: t.eventResize,
                    timelineCoords: t.timelineCoords,
                    onHeightChange: e.handleHeightChange,
                    resourceId: t.resource.id
                })))
            }
            )))
        }
        ,
        t
    }(ii);
    function Cp(e) {
        return {
            resource: new wd(e.context,e.resource)
        }
    }
    var Dp = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this.props
              , n = this.props.renderingHooks
              , r = {
                groupValue: t.groupValue,
                view: this.context.viewApi
            };
            return Yo("tr", {
                ref: t.elRef
            }, Yo(hi, {
                hookProps: r,
                classNames: n.laneClassNames,
                content: n.laneContent,
                didMount: n.laneDidMount,
                willUnmount: n.laneWillUnmount
            }, (function(n, r, o, i) {
                return Yo("td", {
                    ref: n,
                    className: ["fc-timeline-lane", "fc-resource-group", e.context.theme.getClass("tableCellShaded")].concat(r).join(" ")
                }, Yo("div", {
                    style: {
                        height: t.innerHeight
                    },
                    ref: o
                }, i))
            }
            )))
        }
        ,
        t
    }(ii)
      , Rp = function(e) {
        function t() {
            return null !== e && e.apply(this, arguments) || this
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context
              , n = e.rowElRefs
              , o = e.innerHeights;
            return Yo("tbody", null, e.rowNodes.map((function(i, a) {
                if (i.group)
                    return Yo(Dp, {
                        key: i.id,
                        elRef: n.createRef(i.id),
                        groupValue: i.group.value,
                        renderingHooks: i.group.spec,
                        innerHeight: o[a] || ""
                    });
                if (i.resource) {
                    var s = i.resource;
                    return Yo(bp, r({
                        key: i.id,
                        elRef: n.createRef(i.id)
                    }, e.splitProps[s.id], {
                        resource: s,
                        dateProfile: e.dateProfile,
                        tDateProfile: e.tDateProfile,
                        nowDate: e.nowDate,
                        todayRange: e.todayRange,
                        nextDayThreshold: t.options.nextDayThreshold,
                        businessHours: s.businessHours || e.fallbackBusinessHours,
                        innerHeight: o[a] || "",
                        timelineCoords: e.slatCoords,
                        onHeightChange: e.onRowHeightChange
                    }))
                }
                return null
            }
            )))
        }
        ,
        t
    }(ii)
      , wp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.rootElRef = Xo(),
            t.rowElRefs = new ls,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this.props
              , t = this.context;
            return Yo("table", {
                ref: this.rootElRef,
                "aria-hidden": !0,
                className: "fc-scrollgrid-sync-table " + t.theme.getClass("table"),
                style: {
                    minWidth: e.tableMinWidth,
                    width: e.clientWidth,
                    height: e.minHeight
                }
            }, Yo(Rp, {
                rowElRefs: this.rowElRefs,
                rowNodes: e.rowNodes,
                dateProfile: e.dateProfile,
                tDateProfile: e.tDateProfile,
                nowDate: e.nowDate,
                todayRange: e.todayRange,
                splitProps: e.splitProps,
                fallbackBusinessHours: e.fallbackBusinessHours,
                slatCoords: e.slatCoords,
                innerHeights: e.innerHeights,
                onRowHeightChange: e.onRowHeightChange
            }))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.updateCoords()
        }
        ,
        t.prototype.componentDidUpdate = function() {
            this.updateCoords()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.props.onRowCoords && this.props.onRowCoords(null)
        }
        ,
        t.prototype.updateCoords = function() {
            var e, t = this.props;
            t.onRowCoords && null !== t.clientWidth && this.props.onRowCoords(new zo(this.rootElRef.current,(e = this.rowElRefs.currentMap,
            t.rowNodes.map((function(t) {
                return e[t.id]
            }
            ))),!1,!0))
        }
        ,
        t
    }(ii);
    var Tp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.computeHasResourceBusinessHours = cn(_p),
            t.resourceSplitter = new Td,
            t.bgSlicer = new Xc,
            t.slatsRef = Xo(),
            t.state = {
                slatCoords: null
            },
            t.handleEl = function(e) {
                e ? t.context.registerInteractiveComponent(t, {
                    el: e
                }) : t.context.unregisterInteractiveComponent(t)
            }
            ,
            t.handleSlatCoords = function(e) {
                t.setState({
                    slatCoords: e
                }),
                t.props.onSlatCoords && t.props.onSlatCoords(e)
            }
            ,
            t.handleRowCoords = function(e) {
                t.rowCoords = e,
                t.props.onRowCoords && t.props.onRowCoords(e)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this
              , n = t.props
              , r = t.state
              , o = t.context
              , i = n.dateProfile
              , a = n.tDateProfile
              , s = nn(a.slotDuration).unit
              , l = this.computeHasResourceBusinessHours(n.rowNodes)
              , u = this.resourceSplitter.splitProps(n)
              , c = u[""]
              , d = this.bgSlicer.sliceProps(c, i, a.isTimeScale ? null : n.nextDayThreshold, o, i, o.dateProfileGenerator, a, o.dateEnv)
              , p = r.slatCoords && r.slatCoords.dateProfile === n.dateProfile ? r.slatCoords : null;
            return Yo("div", {
                ref: this.handleEl,
                className: ["fc-timeline-body", n.expandRows ? "fc-timeline-body-expandrows" : ""].join(" "),
                style: {
                    minWidth: n.tableMinWidth
                }
            }, Yo(Ga, {
                unit: s
            }, (function(t, r) {
                return Yo(Ko, null, Yo(qc, {
                    ref: e.slatsRef,
                    dateProfile: i,
                    tDateProfile: a,
                    nowDate: t,
                    todayRange: r,
                    clientWidth: n.clientWidth,
                    tableColGroupNode: n.tableColGroupNode,
                    tableMinWidth: n.tableMinWidth,
                    onCoords: e.handleSlatCoords,
                    onScrollLeftRequest: n.onScrollLeftRequest
                }), Yo(Zc, {
                    businessHourSegs: l ? null : d.businessHourSegs,
                    bgEventSegs: d.bgEventSegs,
                    timelineCoords: p,
                    eventResizeSegs: d.eventResize ? d.eventResize.segs : [],
                    dateSelectionSegs: d.dateSelectionSegs,
                    nowDate: t,
                    todayRange: r
                }), Yo(wp, {
                    rowNodes: n.rowNodes,
                    dateProfile: i,
                    tDateProfile: n.tDateProfile,
                    nowDate: t,
                    todayRange: r,
                    splitProps: u,
                    fallbackBusinessHours: l ? n.businessHours : null,
                    clientWidth: n.clientWidth,
                    minHeight: n.expandRows ? n.clientHeight : "",
                    tableMinWidth: n.tableMinWidth,
                    innerHeights: n.rowInnerHeights,
                    slatCoords: p,
                    onRowCoords: e.handleRowCoords,
                    onRowHeightChange: n.onRowHeightChange
                }), o.options.nowIndicator && p && p.isDateInRange(t) && Yo("div", {
                    className: "fc-timeline-now-indicator-container"
                }, Yo(Ts, {
                    isAxis: !1,
                    date: t
                }, (function(e, n, r, i) {
                    return Yo("div", {
                        ref: e,
                        className: ["fc-timeline-now-indicator-line"].concat(n).join(" "),
                        style: zc(p.dateToCoord(t), o.isRtl)
                    }, i)
                }
                ))))
            }
            )))
        }
        ,
        t.prototype.queryHit = function(e, t) {
            var n = this.rowCoords
              , r = n.topToIndex(t);
            if (null != r) {
                var o = this.props.rowNodes[r].resource;
                if (o) {
                    var i = this.slatsRef.current.positionToHit(e);
                    if (i)
                        return {
                            dateProfile: this.props.dateProfile,
                            dateSpan: {
                                range: i.dateSpan.range,
                                allDay: i.dateSpan.allDay,
                                resourceId: o.id
                            },
                            rect: {
                                left: i.left,
                                right: i.right,
                                top: n.tops[r],
                                bottom: n.bottoms[r]
                            },
                            dayEl: i.dayEl,
                            layer: 0
                        }
                }
            }
            return null
        }
        ,
        t
    }(ui);
    function _p(e) {
        for (var t = 0, n = e; t < n.length; t++) {
            var r = n[t].resource;
            if (r && r.businessHours)
                return !0
        }
        return !1
    }
    var xp = function(e) {
        function t() {
            var t = null !== e && e.apply(this, arguments) || this;
            return t.scrollGridRef = Xo(),
            t.timeBodyScrollerElRef = Xo(),
            t.spreadsheetHeaderChunkElRef = Xo(),
            t.rootElRef = Xo(),
            t.ensureScrollGridResizeId = 0,
            t.state = {
                resourceAreaWidthOverride: null
            },
            t.ensureScrollGridResize = function() {
                t.ensureScrollGridResizeId && clearTimeout(t.ensureScrollGridResizeId),
                t.ensureScrollGridResizeId = setTimeout((function() {
                    t.scrollGridRef.current.handleSizing(!1)
                }
                ), Ta.SCROLLGRID_RESIZE_INTERVAL + 1)
            }
            ,
            t
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = e.props
              , n = e.state
              , r = e.context
              , o = r.options
              , i = !t.forPrint && Es(o)
              , a = !t.forPrint && bs(o)
              , s = [{
                type: "header",
                key: "header",
                syncRowHeights: !0,
                isSticky: i,
                chunks: [{
                    key: "datagrid",
                    elRef: this.spreadsheetHeaderChunkElRef,
                    tableClassName: "fc-datagrid-header",
                    rowContent: t.spreadsheetHeaderRows
                }, {
                    key: "divider",
                    outerContent: Yo("td", {
                        role: "presentation",
                        className: "fc-resource-timeline-divider " + r.theme.getClass("tableCellShaded")
                    })
                }, {
                    key: "timeline",
                    content: t.timeHeaderContent
                }]
            }, {
                type: "body",
                key: "body",
                syncRowHeights: !0,
                liquid: !0,
                expandRows: Boolean(o.expandRows),
                chunks: [{
                    key: "datagrid",
                    tableClassName: "fc-datagrid-body",
                    rowContent: t.spreadsheetBodyRows
                }, {
                    key: "divider",
                    outerContent: Yo("td", {
                        role: "presentation",
                        className: "fc-resource-timeline-divider " + r.theme.getClass("tableCellShaded")
                    })
                }, {
                    key: "timeline",
                    scrollerElRef: this.timeBodyScrollerElRef,
                    content: t.timeBodyContent
                }]
            }];
            a && s.push({
                type: "footer",
                key: "footer",
                isSticky: !0,
                chunks: [{
                    key: "datagrid",
                    content: Ss
                }, {
                    key: "divider",
                    outerContent: Yo("td", {
                        role: "presentation",
                        className: "fc-resource-timeline-divider " + r.theme.getClass("tableCellShaded")
                    })
                }, {
                    key: "timeline",
                    content: Ss
                }]
            });
            var l = null != n.resourceAreaWidthOverride ? n.resourceAreaWidthOverride : o.resourceAreaWidth;
            return Yo(ac, {
                ref: this.scrollGridRef,
                elRef: this.rootElRef,
                liquid: !t.isHeightAuto && !t.forPrint,
                collapsibleWidth: !1,
                colGroups: [{
                    cols: t.spreadsheetCols,
                    width: l
                }, {
                    cols: []
                }, {
                    cols: t.timeCols
                }],
                sections: s
            })
        }
        ,
        t.prototype.forceTimeScroll = function(e) {
            this.scrollGridRef.current.forceScrollLeft(2, e)
        }
        ,
        t.prototype.forceResourceScroll = function(e) {
            this.scrollGridRef.current.forceScrollTop(1, e)
        }
        ,
        t.prototype.getResourceScroll = function() {
            return this.timeBodyScrollerElRef.current.scrollTop
        }
        ,
        t.prototype.componentDidMount = function() {
            this.initSpreadsheetResizing()
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.destroySpreadsheetResizing()
        }
        ,
        t.prototype.initSpreadsheetResizing = function() {
            var e = this
              , t = this.context
              , n = t.isRtl
              , r = t.pluginHooks.elementDraggingImpl
              , o = this.spreadsheetHeaderChunkElRef.current;
            if (r) {
                var i, a, s = this.rootElRef.current, l = this.spreadsheetResizerDragging = new r(s,".fc-resource-timeline-divider");
                l.emitter.on("dragstart", (function() {
                    i = o.getBoundingClientRect().width,
                    a = s.getBoundingClientRect().width
                }
                )),
                l.emitter.on("dragmove", (function(t) {
                    var r = i + t.deltaX * (n ? -1 : 1);
                    r = Math.max(r, 30),
                    r = Math.min(r, a - 30),
                    e.setState({
                        resourceAreaWidthOverride: r
                    }, e.ensureScrollGridResize)
                }
                )),
                l.setAutoScrollEnabled(!1)
            }
        }
        ,
        t.prototype.destroySpreadsheetResizing = function() {
            this.spreadsheetResizerDragging && this.spreadsheetResizerDragging.destroy()
        }
        ,
        t
    }(ii)
      , kp = function(e) {
        function t(t, n) {
            var r = e.call(this, t, n) || this;
            return r.processColOptions = cn(Np),
            r.buildTimelineDateProfile = cn(_c),
            r.hasNesting = cn(Pp),
            r.buildRowNodes = cn(qd),
            r.layoutRef = Xo(),
            r.rowNodes = [],
            r.renderedRowNodes = [],
            r.buildRowIndex = cn(Mp),
            r.handleSlatCoords = function(e) {
                r.setState({
                    slatCoords: e
                })
            }
            ,
            r.handleRowCoords = function(e) {
                r.rowCoords = e,
                r.scrollResponder.update(!1)
            }
            ,
            r.handleMaxCushionWidth = function(e) {
                r.setState({
                    slotCushionMaxWidth: Math.ceil(e)
                })
            }
            ,
            r.handleScrollLeftRequest = function(e) {
                r.layoutRef.current.forceTimeScroll(e)
            }
            ,
            r.handleScrollRequest = function(e) {
                var t = r.rowCoords
                  , n = r.layoutRef.current
                  , o = e.rowId || e.resourceId;
                if (t) {
                    if (o) {
                        var i = r.buildRowIndex(r.renderedRowNodes)[o];
                        if (null != i) {
                            var a = null != e.fromBottom ? t.bottoms[i] - e.fromBottom : t.tops[i];
                            n.forceResourceScroll(a)
                        }
                    }
                    return !0
                }
                return null
            }
            ,
            r.handleColWidthChange = function(e) {
                r.setState({
                    spreadsheetColWidths: e
                })
            }
            ,
            r.state = {
                resourceAreaWidth: n.options.resourceAreaWidth,
                spreadsheetColWidths: []
            },
            r
        }
        return n(t, e),
        t.prototype.render = function() {
            var e = this
              , t = this
              , n = t.props
              , r = t.state
              , o = t.context
              , i = o.options
              , a = o.viewSpec
              , s = this.processColOptions(o.options)
              , l = s.superHeaderRendering
              , u = s.groupSpecs
              , c = s.orderSpecs
              , d = s.isVGrouping
              , p = s.colSpecs
              , f = this.buildTimelineDateProfile(n.dateProfile, o.dateEnv, i, o.dateProfileGenerator)
              , h = this.rowNodes = this.buildRowNodes(n.resourceStore, u, c, d, n.resourceEntityExpansions, i.resourcesInitiallyExpanded)
              , v = ["fc-resource-timeline", this.hasNesting(h) ? "" : "fc-resource-timeline-flat", "fc-timeline", !1 === i.eventOverlap ? "fc-timeline-overlap-disabled" : "fc-timeline-overlap-enabled"]
              , g = i.slotMinWidth
              , m = nd(f, g || this.computeFallbackSlotMinWidth(f));
            return Yo(Ci, {
                viewSpec: a
            }, (function(t, i) {
                return Yo("div", {
                    ref: t,
                    className: v.concat(i).join(" ")
                }, Yo(xp, {
                    ref: e.layoutRef,
                    forPrint: n.forPrint,
                    isHeightAuto: n.isHeightAuto,
                    spreadsheetCols: Ip(p, r.spreadsheetColWidths, ""),
                    spreadsheetHeaderRows: function(t) {
                        return Yo(Sp, {
                            superHeaderRendering: l,
                            colSpecs: p,
                            onColWidthChange: e.handleColWidthChange,
                            rowInnerHeights: t.rowSyncHeights
                        })
                    },
                    spreadsheetBodyRows: function(t) {
                        return Yo(Ko, null, e.renderSpreadsheetRows(h, p, t.rowSyncHeights))
                    },
                    timeCols: m,
                    timeHeaderContent: function(t) {
                        return Yo(Fc, {
                            clientWidth: t.clientWidth,
                            clientHeight: t.clientHeight,
                            tableMinWidth: t.tableMinWidth,
                            tableColGroupNode: t.tableColGroupNode,
                            dateProfile: n.dateProfile,
                            tDateProfile: f,
                            slatCoords: r.slatCoords,
                            rowInnerHeights: t.rowSyncHeights,
                            onMaxCushionWidth: g ? null : e.handleMaxCushionWidth
                        })
                    },
                    timeBodyContent: function(t) {
                        return Yo(Tp, {
                            dateProfile: n.dateProfile,
                            clientWidth: t.clientWidth,
                            clientHeight: t.clientHeight,
                            tableMinWidth: t.tableMinWidth,
                            tableColGroupNode: t.tableColGroupNode,
                            expandRows: t.expandRows,
                            tDateProfile: f,
                            rowNodes: h,
                            businessHours: n.businessHours,
                            dateSelection: n.dateSelection,
                            eventStore: n.eventStore,
                            eventUiBases: n.eventUiBases,
                            eventSelection: n.eventSelection,
                            eventDrag: n.eventDrag,
                            eventResize: n.eventResize,
                            resourceStore: n.resourceStore,
                            nextDayThreshold: o.options.nextDayThreshold,
                            rowInnerHeights: t.rowSyncHeights,
                            onSlatCoords: e.handleSlatCoords,
                            onRowCoords: e.handleRowCoords,
                            onScrollLeftRequest: e.handleScrollLeftRequest,
                            onRowHeightChange: t.reportRowHeightChange
                        })
                    }
                }))
            }
            ))
        }
        ,
        t.prototype.renderSpreadsheetRows = function(e, t, n) {
            return e.map((function(e, r) {
                return e.group ? Yo(mp, {
                    key: e.id,
                    id: e.id,
                    spreadsheetColCnt: t.length,
                    isExpanded: e.isExpanded,
                    group: e.group,
                    innerHeight: n[r] || ""
                }) : e.resource ? Yo(gp, {
                    key: e.id,
                    colSpecs: t,
                    rowSpans: e.rowSpans,
                    depth: e.depth,
                    isExpanded: e.isExpanded,
                    hasChildren: e.hasChildren,
                    resource: e.resource,
                    innerHeight: n[r] || ""
                }) : null
            }
            ))
        }
        ,
        t.prototype.componentDidMount = function() {
            this.renderedRowNodes = this.rowNodes,
            this.scrollResponder = this.context.createScrollResponder(this.handleScrollRequest)
        }
        ,
        t.prototype.getSnapshotBeforeUpdate = function() {
            return this.props.forPrint ? {} : {
                resourceScroll: this.queryResourceScroll()
            }
        }
        ,
        t.prototype.componentDidUpdate = function(e, t, n) {
            this.renderedRowNodes = this.rowNodes,
            this.scrollResponder.update(e.dateProfile !== this.props.dateProfile),
            n.resourceScroll && this.handleScrollRequest(n.resourceScroll)
        }
        ,
        t.prototype.componentWillUnmount = function() {
            this.scrollResponder.detach()
        }
        ,
        t.prototype.computeFallbackSlotMinWidth = function(e) {
            return Math.max(30, (this.state.slotCushionMaxWidth || 0) / e.slotsPerLabel)
        }
        ,
        t.prototype.queryResourceScroll = function() {
            var e = this.rowCoords
              , t = this.renderedRowNodes;
            if (e) {
                for (var n = this.layoutRef.current, r = e.bottoms, o = n.getResourceScroll(), i = {}, a = 0; a < r.length; a += 1) {
                    var s = t[a]
                      , l = r[a] - o;
                    if (l > 0) {
                        i.rowId = s.id,
                        i.fromBottom = l;
                        break
                    }
                }
                return i
            }
            return null
        }
        ,
        t
    }(ii);
    function Mp(e) {
        for (var t = {}, n = 0; n < e.length; n += 1)
            t[e[n].id] = n;
        return t
    }
    function Ip(e, t, n) {
        return void 0 === n && (n = ""),
        e.map((function(e, r) {
            return {
                className: e.isMain ? "fc-main-col" : "",
                width: t[r] || e.width || n
            }
        }
        ))
    }
    function Pp(e) {
        for (var t = 0, n = e; t < n.length; t++) {
            var r = n[t];
            if (r.group)
                return !0;
            if (r.resource && r.hasChildren)
                return !0
        }
        return !1
    }
    function Np(e) {
        var t = e.resourceAreaColumns || []
          , n = null;
        t.length ? e.resourceAreaHeaderContent && (n = {
            headerClassNames: e.resourceAreaHeaderClassNames,
            headerContent: e.resourceAreaHeaderContent,
            headerDidMount: e.resourceAreaHeaderDidMount,
            headerWillUnmount: e.resourceAreaHeaderWillUnmount
        }) : t.push({
            headerClassNames: e.resourceAreaHeaderClassNames,
            headerContent: e.resourceAreaHeaderContent || "Resources",
            headerDidMount: e.resourceAreaHeaderDidMount,
            headerWillUnmount: e.resourceAreaHeaderWillUnmount
        });
        for (var o = [], i = [], a = [], s = !1, l = 0, u = t; l < u.length; l++) {
            var c = u[l];
            c.group ? i.push(r(r({}, c), {
                cellClassNames: c.cellClassNames || e.resourceGroupLabelClassNames,
                cellContent: c.cellContent || e.resourceGroupLabelContent,
                cellDidMount: c.cellDidMount || e.resourceGroupLabelDidMount,
                cellWillUnmount: c.cellWillUnmount || e.resourceGroupLaneWillUnmount
            })) : o.push(c)
        }
        var d = o[0];
        if (d.isMain = !0,
        d.cellClassNames = d.cellClassNames || e.resourceLabelClassNames,
        d.cellContent = d.cellContent || e.resourceLabelContent,
        d.cellDidMount = d.cellDidMount || e.resourceLabelDidMount,
        d.cellWillUnmount = d.cellWillUnmount || e.resourceLabelWillUnmount,
        i.length)
            a = i,
            s = !0;
        else {
            var p = e.resourceGroupField;
            p && a.push({
                field: p,
                labelClassNames: e.resourceGroupLabelClassNames,
                labelContent: e.resourceGroupLabelContent,
                labelDidMount: e.resourceGroupLabelDidMount,
                labelWillUnmount: e.resourceGroupLabelWillUnmount,
                laneClassNames: e.resourceGroupLaneClassNames,
                laneContent: e.resourceGroupLaneContent,
                laneDidMount: e.resourceGroupLaneDidMount,
                laneWillUnmount: e.resourceGroupLaneWillUnmount
            })
        }
        for (var f = [], h = 0, v = e.resourceOrder || Md; h < v.length; h++) {
            for (var g = v[h], m = !1, y = 0, S = a; y < S.length; y++) {
                var E = S[y];
                if (E.field === g.field) {
                    E.order = g.order,
                    m = !0;
                    break
                }
            }
            m || f.push(g)
        }
        return {
            superHeaderRendering: n,
            isVGrouping: s,
            groupSpecs: a,
            colSpecs: i.concat(o),
            orderSpecs: f
        }
    }
    kp.addStateEquality({
        spreadsheetColWidths: un
    });
    var Hp = ci({
        deps: [Xu, Jd, rd],
        initialView: "resourceTimelineDay",
        views: {
            resourceTimeline: {
                type: "timeline",
                component: kp,
                needsResourceData: !0,
                resourceAreaWidth: "30%",
                resourcesInitiallyExpanded: !0,
                eventResizableFromStart: !0
            },
            resourceTimelineDay: {
                type: "resourceTimeline",
                duration: {
                    days: 1
                }
            },
            resourceTimelineWeek: {
                type: "resourceTimeline",
                duration: {
                    weeks: 1
                }
            },
            resourceTimelineMonth: {
                type: "resourceTimeline",
                duration: {
                    months: 1
                }
            },
            resourceTimelineYear: {
                type: "resourceTimeline",
                duration: {
                    years: 1
                }
            }
        }
    });
    return Ki.push(yl, Gl, xu, Lu, Bu, Vu, Gu, yc, bc, rd, Jd, rp, lp, Hp),
    e.AbstractResourceDayTableModel = Ud,
    e.BASE_OPTION_DEFAULTS = kn,
    e.BASE_OPTION_REFINERS = xn,
    e.BaseComponent = ii,
    e.BgEvent = Ps,
    e.BootstrapTheme = Uu,
    e.Calendar = Gs,
    e.CalendarApi = Yr,
    e.CalendarContent = Ha,
    e.CalendarDataManager = ea,
    e.CalendarDataProvider = da,
    e.CalendarRoot = Wa,
    e.Component = qo,
    e.ContentHook = gi,
    e.CustomContentRenderContext = vi,
    e.DEFAULT_RESOURCE_ORDER = Md,
    e.DateComponent = ui,
    e.DateEnv = no,
    e.DateProfileGenerator = _i,
    e.DayCellContent = xs,
    e.DayCellRoot = Ms,
    e.DayGridView = Vl,
    e.DayHeader = qa,
    e.DayResourceTableModel = zd,
    e.DaySeriesModel = Za,
    e.DayTable = zl,
    e.DayTableModel = Xa,
    e.DayTableSlicer = Bl,
    e.DayTimeCols = Cu,
    e.DayTimeColsSlicer = bu,
    e.DayTimeColsView = Tu,
    e.DelayedRunner = $i,
    e.Draggable = vl,
    e.ElementDragging = wa,
    e.ElementScrollController = Fo,
    e.Emitter = Bo,
    e.EventApi = Zr,
    e.EventRoot = Ds,
    e.EventSourceApi = Me,
    e.FeaturefulElementDragging = nl,
    e.Fragment = Ko,
    e.Interaction = ba,
    e.ListView = Hu,
    e.MoreLinkRoot = Ls,
    e.MountHook = yi,
    e.NamedTimeZoneImpl = pa,
    e.NowIndicatorRoot = Ts,
    e.NowTimer = Ga,
    e.PointerDragging = Zs,
    e.PositionCache = zo,
    e.RefMap = ls,
    e.RenderHook = hi,
    e.ResourceApi = wd,
    e.ResourceDayHeader = Ad,
    e.ResourceDayTable = ep,
    e.ResourceDayTableModel = Bd,
    e.ResourceDayTableView = tp,
    e.ResourceDayTimeCols = ip,
    e.ResourceDayTimeColsView = ap,
    e.ResourceLabelRoot = Nd,
    e.ResourceSplitter = Td,
    e.ResourceTimelineLane = bp,
    e.ResourceTimelineView = kp,
    e.ScrollController = Vo,
    e.ScrollGrid = ac,
    e.ScrollResponder = ti,
    e.Scroller = ss,
    e.SegHierarchy = fa,
    e.SimpleScrollGrid = Cs,
    e.Slicer = Ka,
    e.Splitter = Co,
    e.SpreadsheetRow = gp,
    e.StandardEvent = Rs,
    e.Table = Ll,
    e.TableDateCell = za,
    e.TableDowCell = Fa,
    e.TableView = Sl,
    e.Theme = jo,
    e.ThirdPartyDraggable = ml,
    e.TimeCols = Su,
    e.TimeColsSlatsCoords = Ql,
    e.TimeColsView = $l,
    e.TimelineCoords = Uc,
    e.TimelineHeader = Fc,
    e.TimelineHeaderRows = Lc,
    e.TimelineLane = Qc,
    e.TimelineLaneBg = Zc,
    e.TimelineLaneSlicer = Xc,
    e.TimelineSlats = qc,
    e.TimelineView = td,
    e.VResourceJoiner = Fd,
    e.VResourceSplitter = Gd,
    e.ViewApi = Vr,
    e.ViewContextType = ni,
    e.ViewRoot = Ci,
    e.WeekNumberRoot = Hs,
    e.WindowScrollController = Go,
    e.addDays = ht,
    e.addDurations = Xt,
    e.addMs = vt,
    e.addWeeks = ft,
    e.allowContextMenu = nt,
    e.allowSelection = et,
    e.applyMutationToEventStore = Ur,
    e.applyStyle = We,
    e.applyStyleProp = Le,
    e.asCleanDays = Zt,
    e.asRoughMinutes = Jt,
    e.asRoughMs = en,
    e.asRoughSeconds = Qt,
    e.binarySearch = Ea,
    e.buildClassNameNormalizer = Si,
    e.buildDayRanges = Du,
    e.buildDayTableModel = Fl,
    e.buildEntryKey = va,
    e.buildEventApis = Kr,
    e.buildEventRangeKey = xr,
    e.buildHashFromArray = function(e, t) {
        for (var n = {}, r = 0; r < e.length; r += 1) {
            var o = t(e[r], r);
            n[o[0]] = o[1]
        }
        return n
    }
    ,
    e.buildIsoString = rn,
    e.buildNavLinkAttrs = ko,
    e.buildResourceFields = Kd,
    e.buildRowNodes = qd,
    e.buildSegCompareObj = br,
    e.buildSegTimeText = wr,
    e.buildSlatCols = nd,
    e.buildSlatMetas = wu,
    e.buildTimeColsModel = _u,
    e.buildTimelineDateProfile = _c,
    e.collectFromHash = zt,
    e.combineEventUis = Zn,
    e.compareByFieldSpec = it,
    e.compareByFieldSpecs = ot,
    e.compareNumbers = ut,
    e.compareObjs = Ut,
    e.computeEarliestSegStart = zs,
    e.computeEdges = Oo,
    e.computeFallbackHeaderFormat = La,
    e.computeHeightAndMargins = function(e) {
        return e.getBoundingClientRect().height + function(e) {
            var t = window.getComputedStyle(e);
            return parseInt(t.marginTop, 10) + parseInt(t.marginBottom, 10)
        }(e)
    }
    ,
    e.computeInnerRect = Ao,
    e.computeRect = Wo,
    e.computeSegDraggable = Cr,
    e.computeSegEndResizable = Rr,
    e.computeSegStartResizable = Dr,
    e.computeShrinkWidth = us,
    e.computeSmallestCellWidth = dt,
    e.computeVisibleDayRange = or,
    e.config = Ta,
    e.constrainPoint = mo,
    e.coordToCss = zc,
    e.coordsToCss = Vc,
    e.createAriaClickAttrs = Ye,
    e.createContext = $o,
    e.createDuration = qt,
    e.createElement = Yo,
    e.createEmptyEventStore = zn,
    e.createEventInstance = Mt,
    e.createEventUi = Yn,
    e.createFormatter = _n,
    e.createPlugin = ci,
    e.createPortal = Jo,
    e.createRef = Xo,
    e.diffDates = ar,
    e.diffDayAndTime = yt,
    e.diffDays = mt,
    e.diffPoints = So,
    e.diffWeeks = gt,
    e.diffWholeDays = Et,
    e.diffWholeWeeks = St,
    e.disableCursor = $e,
    e.elementClosest = Pe,
    e.elementMatches = Ne,
    e.enableCursor = Je,
    e.eventTupleToStore = Un,
    e.filterEventStoreDefs = Fn,
    e.filterHash = Nt,
    e.findDirectChildren = Oe,
    e.findElements = He,
    e.flattenResources = jd,
    e.flexibleCompare = at,
    e.flushSync = Qo,
    e.formatDate = function(e, t) {
        void 0 === t && (t = {});
        var n = uo(t)
          , r = _n(t)
          , o = n.createMarkerMeta(e);
        return o ? n.format(o.marker, r, {
            forcedTzo: o.forcedTzo
        }) : ""
    }
    ,
    e.formatDayString = on,
    e.formatIsoTimeString = an,
    e.formatRange = function(e, t, n) {
        var r = uo("object" == typeof n && n ? n : {})
          , o = _n(n)
          , i = r.createMarkerMeta(e)
          , a = r.createMarkerMeta(t);
        return i && a ? r.formatRange(i.marker, a.marker, o, {
            forcedStartTzo: i.forcedTzo,
            forcedEndTzo: a.forcedTzo,
            isEndExclusive: n.isEndExclusive,
            defaultSeparator: kn.defaultRangeSeparator
        }) : ""
    }
    ,
    e.getAllowYScrolling = ds,
    e.getCanVGrowWithinCell = Eo,
    e.getClippingParents = Lo,
    e.getDateMeta = Ro,
    e.getDayClassNames = wo,
    e.getDefaultEventEnd = Lr,
    e.getElRoot = Be,
    e.getElSeg = mr,
    e.getEntrySpanEnd = ha,
    e.getEventClassNames = _r,
    e.getEventTargetViaRoot = Ue,
    e.getIsRtlScrollbarOnLeft = Po,
    e.getPublicId = Cd,
    e.getRectCenter = yo,
    e.getRelevantEvents = Bn,
    e.getScrollGridClassNames = ms,
    e.getScrollbarWidths = No,
    e.getSectionClassNames = ys,
    e.getSectionHasLiquidHeight = cs,
    e.getSegAnchorAttrs = kr,
    e.getSegMeta = Tr,
    e.getSlotClassNames = To,
    e.getStickyFooterScrollbar = bs,
    e.getStickyHeaderDates = Es,
    e.getUnequalProps = Lt,
    e.getUniqueDomId = Ve,
    e.globalLocales = ro,
    e.globalPlugins = Ki,
    e.greatestDurationDenominator = nn,
    e.groupIntersectingEntries = ga,
    e.guid = Ke,
    e.hasBgRendering = vr,
    e.hasShrinkWidth = gs,
    e.identity = Wn,
    e.interactionSettingsStore = Ra,
    e.interactionSettingsToStore = Da,
    e.intersectRanges = ur,
    e.intersectRects = vo,
    e.intersectSpans = ya,
    e.isArraysEqual = un,
    e.isColPropsEqual = fs,
    e.isDateSelectionValid = Qa,
    e.isDateSpansEqual = Pr,
    e.isGroupsEqual = $d,
    e.isInt = ct,
    e.isInteractionValid = Ja,
    e.isMultiDayRange = ir,
    e.isPropsEqual = Wt,
    e.isPropsValid = ts,
    e.isValidDate = xt,
    e.joinSpans = ma,
    e.listenBySelector = Ge,
    e.mapHash = Ht,
    e.memoize = cn,
    e.memoizeArraylike = pn,
    e.memoizeHashlike = fn,
    e.memoizeObjArg = dn,
    e.mergeEventStores = Vn,
    e.multiplyDuration = Kt,
    e.padStart = st,
    e.parseBusinessHours = fo,
    e.parseClassNames = Gn,
    e.parseDragMeta = xa,
    e.parseEventDef = nr,
    e.parseFieldSpecs = rt,
    e.parseMarker = to,
    e.pointInsideRect = ho,
    e.preventContextMenu = tt,
    e.preventDefault = Fe,
    e.preventSelection = Qe,
    e.rangeContainsMarker = fr,
    e.rangeContainsRange = pr,
    e.rangesEqual = cr,
    e.rangesIntersect = dr,
    e.refineEventDef = er,
    e.refineProps = An,
    e.removeElement = Ie,
    e.removeExact = ln,
    e.render = Zo,
    e.renderChunkContent = ps,
    e.renderFill = Is,
    e.renderMicroColGroup = hs,
    e.renderScrollShim = Ss,
    e.requestJson = Yi,
    e.sanitizeShrinkWidth = vs,
    e.setElSeg = gr,
    e.setRef = li,
    e.setScrollFromLeftEdge = Qu,
    e.sliceEventStore = hr,
    e.sliceEvents = function(e, t) {
        return hr(e.eventStore, e.eventUiBases, e.dateProfile.activeRange, t ? e.nextDayThreshold : null).fg
    }
    ,
    e.sortEventSegs = Er,
    e.startOfDay = bt,
    e.translateRect = go,
    e.triggerDateSelect = Ar,
    e.unmountComponentAtNode = ei,
    e.unpromisify = Uo,
    e.version = "5.11.5",
    e.whenTransitionDone = qe,
    e.wholeDivideDurations = tn,
    Object.defineProperty(e, "__esModule", {
        value: !0
    }),
    e
}({});
