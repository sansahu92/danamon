<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Name - Sandeep Sahu
  Email - san.sahu92@gmail.com
 */
class Danamon
{
  public $username;
  public $password;
  public $url;
  public $cookie;
  public $encodedString;
  public $user_agent;

  public function __construct(){
    $this->url = 'https://www.danamonline.com/onlinebanking/Login/lgn_new.aspx';
    $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    $this->cookie = dirname(__FILE__) . '/cookiedanamon.txt';
  }

  public function landing(){
    $ch = curl_init();
    // Curl to get data login page from BRI
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Host: www.danamonline.com"
    ));
    curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
    curl_setopt($ch, CURLOPT_URL, $this->url);
    // grab URL and pass it to the browser
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );

    $parsed = $this->get_string_between($content, 'encryptData(strAccessCd, PINString) + "', '";blnJVM_OK =');
    //will use for login encryption
    $parsedString = substr($parsed, -23);

    $body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

    $dom = new DOMDocument('1.0', 'UTF-8');

    // set error level
    $internalErrors = libxml_use_internal_errors(true);

    $dom->recover = true;
    $dom->strictErrorChecking = false;
    $dom->loadHTML($body);

    // Restore error level
    libxml_use_internal_errors($internalErrors);

    $inputs = $dom->getElementsByTagName('input');
    $ticket = '';
    $viewstate = '';

    if ($inputs instanceOf DOMNodeList) {
        foreach ($inputs as $input) {
            if ($input->getAttribute('name') == '____Ticket') {
                $ticket =  $input->getAttribute('value');
            }
            if ($input->getAttribute('name') == '__VIEWSTATE') {
                $viewstate =  $input->getAttribute('value');
            }
        }
    }

    $options['parsedString'] = $parsedString;
    $options['form_params'] = [
      '__LoginInd'        => 'true',
      '____Ticket'        => $ticket,
      '__EVENTTARGET'        => '',
      '__EVENTARGUMENT'        => '',
      '__VIEWSTATE'        => $viewstate,
        'txtAccessCode'       => $this->username,
        'txtPin'              => '********',
        'cmdLogin'            => 'Login',
        'hdnRandomNo'       => '',
        'hdnRandomId'         => '',
        'hdnEncodedUs'       => '',
        'hdnLanguage'         => ''
    ];
    return $options;

  }

  public function login(){
    if(!empty($_POST)){
      $data = $_POST;

      $ch = curl_init();
      // Curl to get data login page from BRI
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

      curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
      curl_setopt($ch, CURLOPT_TIMEOUT, 60);
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_COOKIESESSION, true);
      curl_setopt($ch, CURLOPT_POST, TRUE);

      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Host: www.danamonline.com",
        "Origin: https://www.danamonline.com",
        "Referer: https://www.danamonline.com/onlinebanking/Login/lgn_new.aspx"
      ));
      curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
      curl_setopt($ch, CURLOPT_URL, $this->url);
      // grab URL and pass it to the browser
      $content = curl_exec( $ch );
      $err     = curl_errno( $ch );
      $errmsg  = curl_error( $ch );
      $header  = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

      if($header==302){

        //enter into welcome page
        $this->url = 'https://www.danamonline.com/onlinebanking/Default.aspx?usercontrol=Login/lgn_landing&showsplash=1';

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Host: www.danamonline.com",
          "Referer: https://www.danamonline.com/onlinebanking/Login/lgn_new.aspx"
        ));
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        // grab URL and pass it to the browser
        $content = curl_exec( $ch );

//===========Go to transaction page =============================================================
        $this->url = 'https://www.danamonline.com/onlinebanking/default.aspx?usercontrol=DepositAcct/dp_TrxHistory_new';

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Host: www.danamonline.com",
          "Referer: https://www.danamonline.com/onlinebanking/Default.aspx?usercontrol=Login/lgn_landing&showsplash=1"
        ));
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        // grab URL and pass it to the browser
        $content = curl_exec( $ch );
        $body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);

        $dom = new DOMDocument('1.0', 'UTF-8');

        // set error level
        $internalErrors = libxml_use_internal_errors(true);

        $dom->recover = true;
        $dom->strictErrorChecking = false;
        $dom->loadHTML($body);

        // Restore error level
        libxml_use_internal_errors($internalErrors);

        $inputs = $dom->getElementsByTagName('input');
        $__VIEWSTATE = '';
        $____Ticket = '';
        $hdnLanguage = '';
        $hdnWUC = '';
        $hidAcctDet = '';
        $hidCcy = '';
        $hdnPage = '';
        $grp_trxPeriod = '';
        $btnGetDetails = '';

        if ($inputs instanceOf DOMNodeList) {
            foreach ($inputs as $input) {
                if ($input->getAttribute('name') == '__VIEWSTATE') {
                    $__VIEWSTATE =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '____Ticket') {
                    $____Ticket =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == 'hdnWUC') {
                    $hdnWUC =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == 'hdnLanguage') {
                    $hdnLanguage =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:hidAcctDet') {
                    $hidAcctDet =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:hidCcy') {
                    $hidCcy =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:hdnPage') {
                    $hdnPage =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:ddlAcctCCNo') {
                    $ddlAcctCCNo =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:grp_trxPeriod') {
                    $grp_trxPeriod =  $input->getAttribute('value');
                }
                if ($input->getAttribute('name') == '_ctl0:btnGetDetails') {
                    $btnGetDetails =  $input->getAttribute('value');
                }
            }
        }

        $ddlAcctCCNo = '';
        $ddlTrxPeriod = '';

        $optionNodes = $dom->getElementById('_ctl0_ddlAcctCCNo')->getElementsByTagName('option');
        if(!empty($optionNodes)){
          foreach($optionNodes as $optionNode) {
            if(empty($ddlAcctCCNo)){
              $ddlAcctCCNo = $optionNode->getAttribute('value');
            }

          }
        }

        $optionNodes = $dom->getElementById('_ctl0_ddlTrxPeriod')->getElementsByTagName('option');
        if(!empty($optionNodes)){
          foreach($optionNodes as $optionNode) {
            if(empty($ddlTrxPeriod)){
              $ddlTrxPeriod = $optionNode->getAttribute('value');
            }
          }
        }

//========go to form to get statement==========================================================================

        $this->url = 'https://www.danamonline.com/onlinebanking/default.aspx?usercontrol=DepositAcct%2fdp_TrxHistory_new';
        $data = [
          '__EVENTTARGET'        => '',
          '__EVENTARGUMENT'        => '',
          '__VIEWSTATE'        => $__VIEWSTATE,
          '____Ticket'          => $____Ticket,
          'hdnActionCd'        => '',
          'hdnRandomKey'        => '',
          'hdnWUC'        => $hdnWUC,
          'hdnLanguage'        => $hdnLanguage,
          'Portlet1:hdnPortlet'        => '',
          'Portlet1:hdnFunctionId'        => '',
          '_ctl0:hidSortBy'        => '',
          '_ctl0:hidSortExpression'        => '',
          '_ctl0:hidSortField'        => '',
          '_ctl0:hidSortMethod'        => '',
          '_ctl0:hidAcctType'        => '',
          '_ctl0:hidAcctDet'        => $hidAcctDet,
          '_ctl0:hidCcy'        => $hidCcy,
          '_ctl0:hdnPage'        => $hdnPage,
          '_ctl0:ddlAcctCCNo'        => $ddlAcctCCNo,
          '_ctl0:ddlTrxPeriod'        => $ddlTrxPeriod,
          '_ctl0:grp_trxPeriod'        => $grp_trxPeriod,
          '_ctl0:txtFromDate'        => '01/05/2018',
          '_ctl0:txtToDate'        => '24/05/2018',
          '_ctl0:btnGetDetails'        => $btnGetDetails
        ];


        $boundary = '----WebKitFormBoundary'.uniqid();
        $delimiter = $boundary;
        $data = $this->build_data_files($boundary, $data);
        //$data = json_encode($data);
          echo '<pre>';  echo $data;echo '</pre>';
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: multipart/form-data; boundary=" . $delimiter,
          "Content-Length: " . strlen($data),
          "Host: www.danamonline.com",
          "Origin: https://www.danamonline.com",
          "Referer: https://www.danamonline.com/onlinebanking/default.aspx?usercontrol=DepositAcct/dp_TrxHistory_new"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        // grab URL and pass it to the browser
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );

        echo $content;


        //logout from bank
        $this->url = 'https://www.danamonline.com/onlinebanking/Login/lgn_logout.aspx';

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING,  'gzip');

        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_POST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Host: www.danamonline.com",
          "Referer: https://www.danamonline.com/onlinebanking/Login/lgn_logout.aspx"
        ));
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd() . $this->cookie);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        // grab URL and pass it to the browser
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        die('logout done');

      }else{
        return false;
      }
    }else{
      $message = 'nothing to logged in please retry with required fields';
      return false;
    }

  }

  public function generate_encodedString(){

    $landingPage = $this->landing();
    $parsedToken = $landingPage['parsedString'];
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>';
    echo '<script language="javascript">';
    echo 'var bo,aA,bE,ae,dq=2,aQ=16,G=aQ,l=65536,bQ=l>>>1,bx=l*l,bU=l-1,cP=9999999999999998;function ad(r){aA=new Array(bo=r);for(var n=0;n<aA.length;n++)aA[n]=0;bE=new R,(ae=new R).T[0]=1}ad(20);var bf=15,ay=ax(1e15);function R(r){this.T="boolean"==typeof r&&1==r?null:aA.slice(0),this.F=!1}function cv(r){for(var n,t="-"==r.charAt(0),a=t?1:0;a<r.length&&"0"==r.charAt(a);)++a;if(a==r.length)n=new R;else{var e=(r.length-a)%bf;for(0==e&&(e=bf),n=ax(Number(r.substr(a,e))),a+=e;a<r.length;)n=bF(K(n,ay),ax(Number(r.substr(a,bf)))),a+=bf;n.F=t}return n}function bb(r){var n=new R(!0);return n.T=r.T.slice(0),n.F=r.F,n}function ax(r){var n=new R;n.F=r<0,r=Math.abs(r);for(var t=0;r>0;)n.T[t++]=r&bU,r>>=aQ;return n}function bj(r){for(var n="",t=r.length-1;t>-1;--t)n+=r.charAt(t);return n}var am=new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","J","h","l","m","n","o","p","q","r","s","O","u","v","w","x","y","z");function aX(r,n){var t=new R;t.T[0]=n;for(var a=aE(r,t),e=am[a[1].T[0]];1==bk(a[0],bE);)a=aE(a[0],t),bL=a[1].T[0],e+=am[a[1].T[0]];return(r.F?"-":"")+bj(e)}function cw(r){var n=new R;n.T[0]=10;for(var t=aE(r,n),a=String(t[1].T[0]);1==bk(t[0],bE);)t=aE(t[0],n),a+=String(t[1].T[0]);return(r.F?"-":"")+bj(a)}var au=new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");function cK(r){var n="";for(i=0;i<4;++i)n+=au[15&r],r>>>=4;return bj(n)}function bH(r){for(var n="",t=(H(r),H(r));t>-1;--t)n+=cK(r.T[t]);return n}function aW(r){return r>=48&&r<=57?r-48:r>=65&&r<=90?10+r-65:r>=97&&r<=122?10+r-97:0}function cJ(r){for(var n=0,t=Math.min(r.length,4),a=0;a<t;++a)n<<=4,n|=aW(r.charCodeAt(a));return n}function bn(r){for(var n=new R,t=r.length,a=0;t>0;t-=4,++a)n.T[a]=cJ(r.substr(Math.max(t-4,0),Math.min(t,4)));return n}function cg(r,n){var t="-"==r.charAt(0),a=t?1:0,e=new R,b=new R;b.T[0]=1;for(var f=r.length-1;f>=a;f--){e=bF(e,bq(b,aW(r.charCodeAt(f)))),b=bq(b,n)}return e.F=t,e}function cU(r){return(r.F?"-":"")+r.T.join(" ")}function bF(r,n){var t;if(r.F!=n.F)n.F=!n.F,t=C(r,n),n.F=!n.F;else{t=new R;for(var a,e=0,b=0;b<r.T.length;++b)a=r.T[b]+n.T[b]+e,t.T[b]=65535&a,e=Number(a>=l);t.F=r.F}return t}function C(r,n){var t;if(r.F!=n.F)n.F=!n.F,t=bF(r,n),n.F=!n.F;else{var a,e;t=new R,e=0;for(var b=0;b<r.T.length;++b)a=r.T[b]-n.T[b]+e,t.T[b]=65535&a,t.T[b]<0&&(t.T[b]+=l),e=0-Number(a<0);if(-1==e){e=0;for(b=0;b<r.T.length;++b)a=0-t.T[b]+e,t.T[b]=65535&a,t.T[b]<0&&(t.T[b]+=l),e=0-Number(a<0);t.F=!r.F}else t.F=r.F}return t}function H(r){for(var n=r.T.length-1;n>0&&0==r.T[n];)--n;return n}function ak(r){var n,t=H(r),a=r.T[t],e=(t+1)*G;for(n=e;n>e-G&&0==(32768&a);--n)a<<=1;return n}function K(r,n){for(var t,a,e,b=new R,f=H(r),i=H(n),u=0;u<=i;++u){for(t=0,e=u,J=0;J<=f;++J,++e)a=b.T[e]+r.T[J]*n.T[u]+t,b.T[e]=a&bU,t=a>>>aQ;b.T[u+f+1]=t}return b.F=r.F!=n.F,b}function bq(r,n){var t,a,e;result=new R,t=H(r),a=0;for(var b=0;b<=t;++b)e=result.T[b]+r.T[b]*n+a,result.T[b]=e&bU,a=e>>>aQ;return result.T[1+t]=a,result}function bN(r,n,t,a,e){for(var b=Math.min(n+e,r.length),f=n,i=a;f<b;++f,++i)t[i]=r[f]}var bm=new Array(0,32768,49152,57344,61440,63488,64512,65024,65280,65408,65472,65504,65520,65528,65532,65534,65535);function aI(r,n){var t=Math.floor(n/G),a=new R;bN(r.T,0,a.T,t,a.T.length-t);for(var e=n%G,b=G-e,f=a.T.length-1,i=f-1;f>0;--f,--i)a.T[f]=a.T[f]<<e&bU|(a.T[i]&bm[e])>>>b;return a.T[0]=a.T[f]<<e&bU,a.F=r.F,a}var by=new Array(0,1,3,7,15,31,63,127,255,511,1023,2047,4095,8191,16383,32767,65535);function bS(r,n){var t=Math.floor(n/G),a=new R;bN(r.T,t,a.T,0,r.T.length-t);for(var e=n%G,b=G-e,f=0,i=f+1;f<a.T.length-1;++f,++i)a.T[f]=a.T[f]>>>e|(a.T[i]&by[e])<<b;return a.T[a.T.length-1]>>>=e,a.F=r.F,a}function av(r,n){var t=new R;return bN(r.T,0,t.T,n,t.T.length-n),t}function bc(r,n){var t=new R;return bN(r.T,n,t.T,0,t.T.length-n),t}function aa(r,n){var t=new R;return bN(r.T,0,t.T,0,n),t}function bk(r,n){if(r.F!=n.F)return 1-2*Number(r.F);for(var t=r.T.length-1;t>=0;--t)if(r.T[t]!=n.T[t])return r.F?1-2*Number(r.T[t]>n.T[t]):1-2*Number(r.T[t]<n.T[t]);return 0}function aE(r,n){var t,a,e=ak(r),b=ak(n),f=n.F;if(e<b)return r.F?((t=bb(ae)).F=!n.F,r.F=!1,n.F=!1,a=C(n,r),r.F=!0,n.F=f):(t=new R,a=bb(r)),new Array(t,a);t=new R,a=r;for(var i=Math.ceil(b/G)-1,u=0;n.T[i]<bQ;)n=aI(n,1),++u,++b,i=Math.ceil(b/G)-1;a=aI(a,u),e+=u;for(var T=Math.ceil(e/G)-1,o=av(n,T-i);-1!=bk(a,o);)++t.T[T-i],a=C(a,o);for(var c=T;c>i;--c){var h=c>=a.T.length?0:a.T[c],s=c-1>=a.T.length?0:a.T[c-1],v=c-2>=a.T.length?0:a.T[c-2],F=i>=n.T.length?0:n.T[i],g=i-1>=n.T.length?0:n.T[i-1];t.T[c-i-1]=h==F?bU:Math.floor((h*l+s)/F);for(var d=t.T[c-i-1]*(F*l+g),w=h*bx+(s*l+v);d>w;)--t.T[c-i-1],d=t.T[c-i-1]*(F*l|g),w=h*l*l+(s*l+v);(a=C(a,bq(o=av(n,c-i-1),t.T[c-i-1]))).F&&(a=bF(a,o),--t.T[c-i-1])}return a=bS(a,u),t.F=r.F!=f,r.F&&(t=f?bF(t,ae):C(t,ae),a=C(n=bS(n,u),a)),0==a.T[0]&&0==H(a)&&(a.F=!1),new Array(t,a)}function aD(r,n){return aE(r,n)[0]}function aH(r,n){return aE(r,n)[1]}function aP(r,n,t){return aH(K(r,n),t)}function cl(r,n){for(var t=ae,a=r;0!=(1&n)&&(t=K(t,a)),0!=(n>>=1);)a=K(a,a);return t}function dw(r,n,t){for(var a=ae,e=r,b=n;0!=(1&b.T[0])&&(a=aP(a,e,t)),0!=(b=bS(b,1)).T[0]||0!=H(b);)e=aP(e,e,t);return a}function bD(r){this.V=bb(r),this.h=H(this.V)+1;var n=new R;n.T[2*this.h]=1,this.ba=aD(n,this.V),this.aY=new R,this.aY.T[this.h+1]=1,this.bO=aq,this.aw=bg,this.aM=bh}function aq(r){var n=bc(K(bc(r,this.h-1),this.ba),this.h+1),t=C(aa(r,this.h+1),aa(K(n,this.V),this.h+1));t.F&&(t=bF(t,this.aY));for(var a=bk(t,this.V)>=0;a;)a=bk(t=C(t,this.V),this.V)>=0;return t}function bg(r,n){var t=K(r,n);return this.bO(t)}function bh(r,n){var t=new R;t.T[0]=1;for(var a=r,e=n;0!=(1&e.T[0])&&(t=this.aw(t,a)),0!=(e=bS(e,1)).T[0]||0!=H(e);)a=this.aw(a,a);return t}function dF(r,n,t){this.e=bn(r),this.d=bn(n),this.m=bn(t),this.az=2*H(this.m),this.bC=16,this.aT=new bD(this.m)}function dQ(r){return(r<10?"0":"")+String(r)}function db(r,n){for(var t=new Array,a=n.length,e=0;e<a;)t[e]=n.charCodeAt(e),e++;for(;t.length%r.az!=0;)t[e++]=0;var b,f,i,u=t.length,T="";for(e=0;e<u;e+=r.az){for(i=new R,b=0,f=e;f<e+r.az;++b)i.T[b]=t[f++],i.T[b]+=t[f++]<<8;var o=r.aT.aM(i,r.e);T+=(16==r.bC?bH(o):aX(o,r.bC))+" "}return T.substring(0,T.length-1)}function show(r){var n,t="";for(n=0;n<r.T.length;n++)t+=r.T[n]+",";alert(t)}function encryptData(r,n){return db(key,r+":"+n)}function encryptChangePin(r,n,t){return db(key,r+":"+n+":"+t)}ad(131),key=new dF("10001","","a4d080919595f0e722755e03a81417fc56b05ec346bec242092f28de5dff757e4a638cdcfe03d3d7b9411e13a52447f78eb3df07b0f2207f1bebb04bbfe00807ac8dbaca5bad5f7560e4d23011b38c10e614620d0209c5c653717455acb445a99c7f276263908d728e1e631d48c8336018a30321b07cf1bfe255dbda707c8539");var strAccessCd="'.$this->username.'",PINString="'.$this->password.'",parsedString="'.$parsedToken.'",EncodedString=encryptData(strAccessCd,PINString)+parsedString;$(document).ready(function(){$("#hdnEncodedString").val(EncodedString)});';
    echo '$(document).ready(function(){setTimeout(function() {$(".loginBank").submit();},800);});';
    echo '</script>';
    echo '<form method="POST" action="" class="loginBank" name="encodeStringForm">';
    if(!empty($landingPage['form_params'])){
      foreach ($landingPage['form_params'] as $key =>$value) {
        echo '<input type="hidden" name="'.$key.'" value="'.$value.'" id="'.$key.'" />';
      }
    }
    echo '<input type="hidden" name="hdnEncodedString" id="hdnEncodedString" />';
    echo '</form>';
  }

  public function get_string_between($string, $start, $end){
      $string = ' ' . $string;
      $ini = strpos($string, $start);
      if ($ini == 0) return '';
      $ini += strlen($start);
      $len = strpos($string, $end, $ini) - $ini;
      return substr($string, $ini, $len);
  }

  public function build_data_files($boundary, $fields){
      $data = '';
      $eol = "\r\n";

      $delimiter = '--' . $boundary;

      foreach ($fields as $name => $content) {
          $data .= $delimiter . $eol
              . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
              . $content . $eol;
      }
      $data .= "--" . $delimiter . "--".$eol;


      return $data;
  }

}

$danamon = new Danamon();
$danamon->username = 'username';
$danamon->password = 'password';
if(isset($_POST) && !empty($_POST['hdnEncodedString'])){
  $check_login = $danamon->login();
}else{
  $danamon->generate_encodedString();
}
