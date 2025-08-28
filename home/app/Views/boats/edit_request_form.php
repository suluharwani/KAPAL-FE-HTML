public function getRequestDetails($requestId)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
    }

    $requestModel = new \App\Models\RequestOpenTripsModel();
    $boatModel = new BoatModel();
    $routeModel = new RouteModel();
    
    // Get request details
    $request = $requestModel->find($requestId);
    
    if (!$request) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Request not found']);
    }
    
    // Get boat details
    $boat = $boatModel->find($request['boat_id']);
    $request['boat_name'] = $boat['boat_name'] ?? 'N/A';
    $request['boat_type'] = $boat['boat_type'] ?? 'N/A';
    $request['capacity'] = $boat['capacity'] ?? 'N/A';
    
    // Get route details
    $route = $routeModel->find($request['route_id']);
    $request['departure_island_name'] = 'N/A';
    $request['arrival_island_name'] = 'N/A';
    
    if ($route) {
        $islandModel = new IslandModel();
        $departureIsland = $islandModel->find($route['departure_island_id']);
        $arrivalIsland = $islandModel->find($route['arrival_island_id']);
        
        $request['departure_island_name'] = $departureIsland['island_name'] ?? 'N/A';
        $request['arrival_island_name'] = $arrivalIsland['island_name'] ?? 'N/A';
    }
    
    // Generate HTML form langsung tanpa file view
    $boats = $boatModel->findAll();
    $routes = $routeModel->getRoutesWithIslands();
    
    $html = '
    <div class="row">
        <input type="hidden" name="request_id" value="' . $request['request_id'] . '">
        
        <div class="col-md-6 mb-3">
            <label for="editBoat" class="form-label">Kapal</label>
            <select class="form-select" id="editBoat" name="boat_id" required>
                <option value="" disabled>Pilih Kapal</option>';
    
    foreach ($boats as $boat) {
        $selected = $boat['boat_id'] == $request['boat_id'] ? 'selected' : '';
        $html .= '<option value="' . $boat['boat_id'] . '" ' . $selected . '>
                    ' . $boat['boat_name'] . ' (' . $boat['boat_type'] . ' - Kapasitas: ' . $boat['capacity'] . ' orang)
                </option>';
    }
    
    $html .= '</select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="editRoute" class="form-label">Rute</label>
            <select class="form-select" id="editRoute" name="route_id" required>
                <option value="" disabled>Pilih Rute</option>';
    
    foreach ($routes as $route) {
        $selected = $route['route_id'] == $request['route_id'] ? 'selected' : '';
        $html .= '<option value="' . $route['route_id'] . '" ' . $selected . '>
                    ' . $route['departure_island_name'] . ' - ' . $route['arrival_island_name'] . '
                </option>';
    }
    
    $html .= '</select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="editProposedDate" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="editProposedDate" name="proposed_date" 
                   value="' . $request['proposed_date'] . '" min="' . date('Y-m-d') . '" required>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="editProposedTime" class="form-label">Waktu</label>
            <input type="time" class="form-control" id="editProposedTime" name="proposed_time" 
                   value="' . date('H:i', strtotime($request['proposed_time'])) . '" required>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="editNotes" class="form-label">Catatan (Opsional)</label>
        <textarea class="form-control" id="editNotes" name="notes" rows="3" 
                  placeholder="Tambahkan catatan atau permintaan khusus">' . ($request['notes'] ?? '') . '</textarea>
    </div>
    
    <div class="alert alert-info">
        <h6><i class="fas fa-info-circle"></i> Informasi:</h6>
        <ul class="mb-0">
            <li>Kapasitas kapal: <strong>' . $request['capacity'] . ' orang</strong></li>
            <li>Rute: <strong>' . $request['departure_island_name'] . ' - ' . $request['arrival_island_name'] . '</strong></li>
            <li>Status saat ini: <span class="badge bg-warning">' . ucfirst($request['status']) . '</span></li>
        </ul>
    </div>';
    
    return $this->response->setJSON([
        'success' => true,
        'html' => $html
    ]);
}