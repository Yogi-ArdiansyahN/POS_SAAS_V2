<?php

namespace App\Filters;

use App\Models\CookiesModel;
use App\Models\Users;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IsLoggedIn implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('text');
        $uri = service('uri');
        $user = new Users();
        $segment = $uri->getSegment(1);

        if (empty(session()->get('users'))) {
            return redirect()->to(base_url('/'));
        }

        $users = session()->get('users');

        // validasi role
        $role = session()->get('users')['role'];
        if ($role != $segment) {
            return redirect()->back();
        }

        $users = $user->where('username', $users['username'])->first();

        // dd($users);
        if (!$users) {
            // session()->setFlashdata('errors', 'Please login first.');
            session()->setFlashdata('errors', 'Silahkan login terlebih dahulu.');
            return redirect()->to(base_url('/'));
        }

        return;
        // }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
