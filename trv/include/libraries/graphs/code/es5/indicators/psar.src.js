/**
 * @license Highstock JS v11.0.0 (2023-04-26)
 *
 * Parabolic SAR Indicator for Highcharts Stock
 *
 * (c) 2010-2021 Grzegorz Blachliński
 *
 * License: www.highcharts.com/license
 */
(function (factory) {
    if (typeof module === 'object' && module.exports) {
        factory['default'] = factory;
        module.exports = factory;
    } else if (typeof define === 'function' && define.amd) {
        define('highcharts/indicators/psar', ['highcharts', 'highcharts/modules/stock'], function (Highcharts) {
            factory(Highcharts);
            factory.Highcharts = Highcharts;
            return factory;
        });
    } else {
        factory(typeof Highcharts !== 'undefined' ? Highcharts : undefined);
    }
}(function (Highcharts) {
    'use strict';
    var _modules = Highcharts ? Highcharts._modules : {};
    function _registerModule(obj, path, args, fn) {
        if (!obj.hasOwnProperty(path)) {
            obj[path] = fn.apply(null, args);

            if (typeof CustomEvent === 'function') {
                window.dispatchEvent(
                    new CustomEvent(
                        'HighchartsModuleLoaded',
                        { detail: { path: path, module: obj[path] }
                    })
                );
            }
        }
    }
    _registerModule(_modules, 'Stock/Indicators/PSAR/PSARIndicator.js', [_modules['Core/Series/SeriesRegistry.js'], _modules['Core/Utilities.js']], function (SeriesRegistry, U) {
        /* *
         *
         *  Parabolic SAR indicator for Highcharts Stock
         *
         *  (c) 2010-2021 Grzegorz Blachliński
         *
         *  License: www.highcharts.com/license
         *
         *  !!!!!!! SOURCE GETS TRANSPILED BY TYPESCRIPT. EDIT TS FILE ONLY. !!!!!!!
         *
         * */
        var __extends = (this && this.__extends) || (function () {
            var extendStatics = function (d, b) {
                extendStatics = Object.setPrototypeOf ||
                    ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
                    function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
                return extendStatics(d, b);
            };
            return function (d, b) {
                if (typeof b !== "function" && b !== null)
                    throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
                extendStatics(d, b);
                function __() { this.constructor = d; }
                d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
            };
        })();
        var SMAIndicator = SeriesRegistry.seriesTypes.sma;
        var merge = U.merge;
        /* *
         *
         *  Functions
         *
         * */
        // Utils:
        function toFixed(a, n) {
            return parseFloat(a.toFixed(n));
        }
        function calculateDirection(previousDirection, low, high, PSAR) {
            if ((previousDirection === 1 && low > PSAR) ||
                (previousDirection === -1 && high > PSAR)) {
                return 1;
            }
            return -1;
        }
        /* *
         * Method for calculating acceleration factor
         * dir - direction
         * pDir - previous Direction
         * eP - extreme point
         * pEP - previous extreme point
         * inc - increment for acceleration factor
         * maxAcc - maximum acceleration factor
         * initAcc - initial acceleration factor
         */
        function getAccelerationFactor(dir, pDir, eP, pEP, pAcc, inc, maxAcc, initAcc) {
            if (dir === pDir) {
                if (dir === 1 && (eP > pEP)) {
                    return (pAcc === maxAcc) ? maxAcc : toFixed(pAcc + inc, 2);
                }
                if (dir === -1 && (eP < pEP)) {
                    return (pAcc === maxAcc) ? maxAcc : toFixed(pAcc + inc, 2);
                }
                return pAcc;
            }
            return initAcc;
        }
        function getExtremePoint(high, low, previousDirection, previousExtremePoint) {
            if (previousDirection === 1) {
                return (high > previousExtremePoint) ? high : previousExtremePoint;
            }
            return (low < previousExtremePoint) ? low : previousExtremePoint;
        }
        function getEPMinusPSAR(EP, PSAR) {
            return EP - PSAR;
        }
        function getAccelerationFactorMultiply(accelerationFactor, EPMinusSAR) {
            return accelerationFactor * EPMinusSAR;
        }
        /* *
         * Method for calculating PSAR
         * pdir - previous direction
         * sDir - second previous Direction
         * PSAR - previous PSAR
         * pACCMultiply - previous acceleration factor multiply
         * sLow - second previous low
         * pLow - previous low
         * sHigh - second previous high
         * pHigh - previous high
         * pEP - previous extreme point
         */
        function getPSAR(pdir, sDir, PSAR, pACCMulti, sLow, pLow, pHigh, sHigh, pEP) {
            if (pdir === sDir) {
                if (pdir === 1) {
                    return (PSAR + pACCMulti < Math.min(sLow, pLow)) ?
                        PSAR + pACCMulti :
                        Math.min(sLow, pLow);
                }
                return (PSAR + pACCMulti > Math.max(sHigh, pHigh)) ?
                    PSAR + pACCMulti :
                    Math.max(sHigh, pHigh);
            }
            return pEP;
        }
        /* *
         *
         *  Class
         *
         * */
        /**
         * The Parabolic SAR series type.
         *
         * @private
         * @class
         * @name Highcharts.seriesTypes.psar
         *
         * @augments Highcharts.Series
         */
        var PSARIndicator = /** @class */ (function (_super) {
            __extends(PSARIndicator, _super);
            function PSARIndicator() {
                /* *
                 *
                 *  Static Properties
                 *
                 * */
                var _this = _super !== null && _super.apply(this, arguments) || this;
                /* *
                 *
                 *  Properties
                 *
                 * */
                _this.data = void 0;
                _this.nameComponents = void 0;
                _this.points = void 0;
                _this.options = void 0;
                return _this;
            }
            /* *
             *
             *  Functions
             *
             * */
            PSARIndicator.prototype.getValues = function (series, params) {
                var xVal = series.xData, yVal = series.yData, maxAccelerationFactor = params.maxAccelerationFactor, increment = params.increment, 
                // Set initial acc factor (for every new trend!)
                initialAccelerationFactor = params.initialAccelerationFactor, decimals = params.decimals, index = params.index, PSARArr = [], xData = [], yData = [];
                var accelerationFactor = params.initialAccelerationFactor, direction, 
                // Extreme point is the lowest low for falling and highest high
                // for rising psar - and we are starting with falling
                extremePoint = yVal[0][1], EPMinusPSAR, accelerationFactorMultiply, newDirection, previousDirection = 1, prevLow, prevPrevLow, prevHigh, prevPrevHigh, PSAR = yVal[0][2], newExtremePoint, high, low, ind;
                if (index >= yVal.length) {
                    return;
                }
                for (ind = 0; ind < index; ind++) {
                    extremePoint = Math.max(yVal[ind][1], extremePoint);
                    PSAR = Math.min(yVal[ind][2], toFixed(PSAR, decimals));
                }
                direction = (yVal[ind][1] > PSAR) ? 1 : -1;
                EPMinusPSAR = getEPMinusPSAR(extremePoint, PSAR);
                accelerationFactor = params.initialAccelerationFactor;
                accelerationFactorMultiply = getAccelerationFactorMultiply(accelerationFactor, EPMinusPSAR);
                PSARArr.push([xVal[index], PSAR]);
                xData.push(xVal[index]);
                yData.push(toFixed(PSAR, decimals));
                for (ind = index + 1; ind < yVal.length; ind++) {
                    prevLow = yVal[ind - 1][2];
                    prevPrevLow = yVal[ind - 2][2];
                    prevHigh = yVal[ind - 1][1];
                    prevPrevHigh = yVal[ind - 2][1];
                    high = yVal[ind][1];
                    low = yVal[ind][2];
                    // Null points break PSAR
                    if (prevPrevLow !== null &&
                        prevPrevHigh !== null &&
                        prevLow !== null &&
                        prevHigh !== null &&
                        high !== null &&
                        low !== null) {
                        PSAR = getPSAR(direction, previousDirection, PSAR, accelerationFactorMultiply, prevPrevLow, prevLow, prevHigh, prevPrevHigh, extremePoint);
                        newExtremePoint = getExtremePoint(high, low, direction, extremePoint);
                        newDirection = calculateDirection(previousDirection, low, high, PSAR);
                        accelerationFactor = getAccelerationFactor(newDirection, direction, newExtremePoint, extremePoint, accelerationFactor, increment, maxAccelerationFactor, initialAccelerationFactor);
                        EPMinusPSAR = getEPMinusPSAR(newExtremePoint, PSAR);
                        accelerationFactorMultiply = getAccelerationFactorMultiply(accelerationFactor, EPMinusPSAR);
                        PSARArr.push([xVal[ind], toFixed(PSAR, decimals)]);
                        xData.push(xVal[ind]);
                        yData.push(toFixed(PSAR, decimals));
                        previousDirection = direction;
                        direction = newDirection;
                        extremePoint = newExtremePoint;
                    }
                }
                return {
                    values: PSARArr,
                    xData: xData,
                    yData: yData
                };
            };
            /**
             * Parabolic SAR. This series requires `linkedTo`
             * option to be set and should be loaded
             * after `stock/indicators/indicators.js` file.
             *
             * @sample stock/indicators/psar
             *         Parabolic SAR Indicator
             *
             * @extends      plotOptions.sma
             * @since        6.0.0
             * @product      highstock
             * @requires     stock/indicators/indicators
             * @requires     stock/indicators/psar
             * @optionparent plotOptions.psar
             */
            PSARIndicator.defaultOptions = merge(SMAIndicator.defaultOptions, {
                lineWidth: 0,
                marker: {
                    enabled: true
                },
                states: {
                    hover: {
                        lineWidthPlus: 0
                    }
                },
                /**
                 * @excluding period
                 */
                params: {
                    period: void 0,
                    /**
                     * The initial value for acceleration factor.
                     * Acceleration factor is starting with this value
                     * and increases by specified increment each time
                     * the extreme point makes a new high.
                     * AF can reach a maximum of maxAccelerationFactor,
                     * no matter how long the uptrend extends.
                     */
                    initialAccelerationFactor: 0.02,
                    /**
                     * The Maximum value for acceleration factor.
                     * AF can reach a maximum of maxAccelerationFactor,
                     * no matter how long the uptrend extends.
                     */
                    maxAccelerationFactor: 0.2,
                    /**
                     * Acceleration factor increases by increment each time
                     * the extreme point makes a new high.
                     *
                     * @since 6.0.0
                     */
                    increment: 0.02,
                    /**
                     * Index from which PSAR is starting calculation
                     *
                     * @since 6.0.0
                     */
                    index: 2,
                    /**
                     * Number of maximum decimals that are used in PSAR calculations.
                     *
                     * @since 6.0.0
                     */
                    decimals: 4
                }
            });
            return PSARIndicator;
        }(SMAIndicator));
        SeriesRegistry.registerSeriesType('psar', PSARIndicator);
        /* *
         *
         *  Default Export
         *
         * */
        /* *
         *
         *  API Options
         *
         * */
        /**
         * A `PSAR` series. If the [type](#series.psar.type) option is not specified, it
         * is inherited from [chart.type](#chart.type).
         *
         * @extends   series,plotOptions.psar
         * @since     6.0.0
         * @product   highstock
         * @excluding dataParser, dataURL
         * @requires  stock/indicators/indicators
         * @requires  stock/indicators/psar
         * @apioption series.psar
         */
        ''; // to include_once the above in the js output

        return PSARIndicator;
    });
    _registerModule(_modules, 'masters/indicators/psar.src.js', [], function () {


    });
}));