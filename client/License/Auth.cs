using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using ShoebotsLoader.Crypto;
using ShoebotsLoader.Data;
using ShoebotsLoader.Http;

namespace ShoebotsLoader.License
{
    [System.Reflection.Obfuscation(Exclude = false, Feature = "+koi")]

    public enum AuthStatus
    {
        Success,
        MissingLicense,
        Unknown, UnknownHttpError,
        InvalidJson,
        InvalidEndpoint, InvalidLicense, DisabledLicense, InvalidProduct, InvalidHardwareId, Expired
    }
    [System.Reflection.Obfuscation(Exclude = false, Feature = "+koi")]

    public struct AuthResponse
    {
        public AuthStatus StatusCode;
        public string StatusMessage;
        public byte[] Data;
    }

    [System.Reflection.Obfuscation(Exclude = false, Feature = "+koi")]

    public class Auth
    {
        public static AuthResponse Authorize(AuthConfig config, ProductConfig product, string license)
        {
            var authResponse = new AuthResponse
            {
                StatusCode = AuthStatus.Unknown,
                StatusMessage = "",
                Data = null
            };

            if (string.IsNullOrEmpty(license))
            {
                authResponse.StatusCode = AuthStatus.MissingLicense;
                authResponse.StatusMessage = "missing license";

                return authResponse;
            }

            var dhKeyEx = new DhKeyExchange();

            var authHwid = Hwid.Generate(config.HwidSalt);
            var authRequest = new Request(config.ApiUrl.EndsWith("/") ? config.ApiUrl + "authorize" : config.ApiUrl + "/authorize")
                .Method(RequestMethod.Post)
                .PostParam("license", license)
                .PostParam("data", dhKeyEx.PublicKey())
                .PostParam("hwid", authHwid);

            var authConnection = new Connection();
            //setup pinned certificate?

            var apiResponse = authConnection.PerformRequest(authRequest);
            if (apiResponse.code != 200 || string.IsNullOrEmpty(apiResponse.body))
            {
                authResponse.StatusCode = AuthStatus.UnknownHttpError;
                authResponse.StatusMessage = "unknown http error";

                return authResponse;
            }

            var jsonResponse = (Dictionary<string, object>)apiResponse.body.FromJson<object>();
            if (!jsonResponse.ContainsKey("key") || !jsonResponse.ContainsKey("data"))
            {
                if (!jsonResponse.ContainsKey("error"))
                {
                    authResponse.StatusMessage = "unknown error";
                    return authResponse;
                }

                var errorResponse = (Dictionary<string, object>) jsonResponse["error"];
                if (!errorResponse.ContainsKey("code") || !errorResponse.ContainsKey("message"))
                {
                    authResponse.StatusMessage = "unknown error";
                    return authResponse;
                }

                var errorCode = (int) errorResponse["code"];
                authResponse.StatusMessage = (string) errorResponse["message"];

                switch (errorCode)
                {
                    case -1000:
                        authResponse.StatusCode = AuthStatus.InvalidEndpoint;
                        break;
                    case -1001:
                        authResponse.StatusCode = AuthStatus.InvalidLicense;
                        break;
                    case -1002:
                        authResponse.StatusCode = AuthStatus.DisabledLicense;
                        break;
                    case -1003:
                        authResponse.StatusCode = AuthStatus.InvalidProduct;
                        break;
                    case -1004:
                        authResponse.StatusCode = AuthStatus.InvalidHardwareId;
                        break;
                    case -1005:
                        authResponse.StatusCode = AuthStatus.Expired;
                        break;
                    default:
                        authResponse.StatusCode = AuthStatus.Unknown;
                        break;
                }

                return authResponse;
            }

            var remotePubKey = (string) jsonResponse["key"];
            var authData = (string) jsonResponse["data"];

            var sharedHash = dhKeyEx.SharedHash(remotePubKey);

            //decode base64
            authResponse.Data = Base64.Decode(authData);

            //decrypt rsa
            var rsaCrypt = new Rsa(product.PublicKey);
            authResponse.Data = rsaCrypt.PublicDecrypt(authResponse.Data);

            //decrypt rc4 dh key
            var rc4Crypt = new Rc4(sharedHash);
            authResponse.Data = rc4Crypt.Crypt(authResponse.Data);

            //decrypt rc4 hwid
            rc4Crypt.Key(authHwid);
            authResponse.Data = rc4Crypt.Crypt(authResponse.Data);

            //license parser validate_crc
            if (!Parser.ValidateCrc(authResponse.Data))
            {
                authResponse.StatusCode = AuthStatus.InvalidLicense;
                return authResponse;
            }

            authResponse.StatusCode = AuthStatus.Success;

            return authResponse;
        }
    }
}
