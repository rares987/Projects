package Repository;

import DataBaseAccess.DBAccess;
import Entity.CameraEntity;
import Entity.CaminEntity;

import javax.persistence.EntityManager;
import javax.persistence.Query;
import java.math.BigInteger;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

public class RepositoryCamera implements Repository<CameraEntity> {

    private EntityManager em = DBAccess.getInstance();

    private RepositoryCamin localCaminRepository = new RepositoryCamin();

    @Override
    public int count() {
        Query countQuery = em.createNativeQuery("SELECT count(*) FROM Camera");
        Object result = countQuery.getSingleResult();
        return ((BigInteger) result).intValue();
    }

    @Override
    public void deleteAll() {
        Query deleteQuery = em.createNativeQuery("DELETE from Camera");
        deleteQuery.executeUpdate();
    }

    @Override
    public void deleteById(int id) {
        Query deleteQuery = em.createNativeQuery("DELETE from Camera where id = ?1");
        deleteQuery.setParameter(1, id);
        deleteQuery.executeUpdate();
    }

    @Override
    public boolean existsById(int id) {
        Query existsQuery = em.createNativeQuery("select count(*) FROM Camera WHERE id = ?1");
        existsQuery.setParameter(1, id);
        Object result = existsQuery.getSingleResult();
        if (((BigInteger) result).intValue() == 0) {
            return false;
        }
        return true;
    }

    @Override
    public List<CameraEntity> findAll() {
        ArrayList<CameraEntity> resultList = new ArrayList<>();
        Query findQuery = em.createNativeQuery("SELECT * FROM camera");

        List<Object> result = (List<Object>) findQuery.getResultList();
        Iterator itr = result.iterator();

        while (itr.hasNext()) {
            CameraEntity camera = new CameraEntity();
            Object[] obj = (Object[]) itr.next();
            Integer id = Integer.parseInt(String.valueOf(obj[0]));
            Integer idCamin = Integer.parseInt(String.valueOf(obj[1]));
            Integer capacitate = Integer.parseInt(String.valueOf(obj[2]));
            camera.setId(id);
            camera.setIdCamin(idCamin);
            camera.setReferencedCamin(localCaminRepository.findById(idCamin));
            camera.setCapacitate(capacitate);
            resultList.add(camera);
        }
        return resultList;
    }

    @Override
    public CameraEntity findById(int queryId) {
        Query existsQuery = em.createNativeQuery("select id FROM camera WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        Object result = existsQuery.getSingleResult();
        Integer id = ((Number) result).intValue();

        Integer idCamin=null;
        existsQuery = em.createNativeQuery("select id_camin FROM camera WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        if (result == null)
            idCamin =null;
        idCamin = ((Number) result).intValue();

        existsQuery = em.createNativeQuery("select capacitate FROM camera WHERE id = ?1");
        existsQuery.setParameter(1, queryId);
        result = existsQuery.getSingleResult();
        Integer capacitate = ((Number) result).intValue();

        CameraEntity returnCameraEntity = new CameraEntity();
        returnCameraEntity.setId(id);
        returnCameraEntity.setIdCamin(idCamin);
        returnCameraEntity.setReferencedCamin(localCaminRepository.findById(idCamin));
        returnCameraEntity.setCapacitate(capacitate);

        return returnCameraEntity;
    }

    @Override
    public void save(CameraEntity camera) {
            em.persist(camera);
    }
}
